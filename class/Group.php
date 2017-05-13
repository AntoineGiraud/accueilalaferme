<?php

namespace AccueilALaFerme;

class Group {

    public $pk;
    public $name;
    public $adress_pk;
    public $adress;
    public $phone;
    public $comment;
    public $is_family;
    public $event_pk;
    public $was_canceled;
    public $groupLinks;

    function __construct($pk, $DB, $curPerson=null) {
        $this->DB = $DB;
        $res = $DB->query('SELECT * FROM group_link');
        $this->groupLinks = [];
        foreach ($res as $row)
            $this->groupLinks[$row['slug']] = $row;

        $this->prop = null;
        if ($pk === null) {
            $data = \AccueilALaFerme\Group::getBasicFields($curPerson);
            $this->prop = $data['prop'];
            $this->persons = $data['persons'];
            return $this;
        }

        if (!empty($pk))
            $this->prop = $DB->queryFirst("SELECT g.*,
                                                a.pk addr_pk, a.street addr_street, a.postal_code addr_postal_code,
                                                    a.city addr_city, a.region addr_region, a.country addr_country,
                                                e.name event_name, e.slug event_slug
                                            FROM `group` g
                                                LEFT JOIN address a ON a.pk = g.address_pk
                                                LEFT JOIN event e ON e.pk = g.event_pk
                                            WHERE g.pk = :pk", ['pk' => $pk]);
        if (empty($this->prop))
            throw new \Exception("unkown group");
        else
            $this->prop['address'] = [
                'pk' => $this->prop['addr_pk'], 'street' => $this->prop['addr_street'], 'postal_code' => $this->prop['addr_postal_code'], 'city' => $this->prop['addr_city'], 'region' => $this->prop['addr_region'], 'country' => $this->prop['addr_country']
            ];
            unset($this->prop['addr_pk'], $this->prop['addr_street'], $this->prop['addr_postal_code'], $this->prop['addr_city'], $this->prop['addr_region'], $this->prop['addr_country']);

            $res = $DB->query( "SELECT p.*, phg.can_manage, phg.group_link_pk, phg.was_removed, phg.event_pk, gl.slug link
                                FROM person_has_group phg
                                    LEFT JOIN person p ON phg.person_id = p.pk
                                    LEFT JOIN group_link gl ON phg.group_link_pk = gl.pk
                                WHERE (was_removed IS NULL OR was_removed=0) AND phg.group_id = :group_id", ['group_id' => $pk]);
            $this->persons = [];
            $this->admins = [];
            foreach ($res as $p) {
                if ($p['can_manage'])
                    $this->admins[$p['pk']] = $p['can_manage'];
                $this->persons[$p['pk']] = $p;

            }
    }

    public function saveGroup($post, $event_pk=null) {
        extract($this->validateData($post));
        if (!empty($errors)) {
            $this->errors = $errors;
            $this->persons = $post['persons'];
            foreach ($errors['persons'] as $key => $e)
                $this->persons[$key]['errors'] = $e;
            throw new \Exception("Des erreurs sont présentes dans le formulaire.", 1);
        } else {
            // Save address
            $this->prop['address'] = \AccueilALaFerme\Address::save($this->DB, $post['address'], $this->prop['address']);

            // Save group
            if (empty($this->prop['pk'])) { // Insert Group
                $this->prop['pk'] = $this->DB->query(
                            "INSERT INTO `group` (`name`, `address_pk`, `phone`, `is_family`, `event_pk`)
                            VALUES (:name, :address_pk, :phone, :is_family, :event_pk)", [
                                'name' => $post['name'],
                                'address_pk' => $this->prop['address']['pk'],
                                'phone' => $post['phone'],
                                'is_family' => $post['is_family'],
                                'event_pk' => $event_pk
                            ]);
            } else { // update group
                $update = [];
                if ($post['name'] !== $this->prop['name'])
                    $update['name'] = 'name = "'.$post['name'].'"';
                if ($post['phone'] !== $this->prop['phone'])
                    $update['phone'] = 'phone = "'.$post['phone'].'"';
                if ($post['is_family']*1 !== $this->prop['is_family']*1)
                    $update['is_family'] = 'is_family = '.$post['is_family'].'';
                if ($post['address']['pk'] !== $this->prop['address']['pk'])
                    $update['address_pk'] = 'address_pk = '.(empty($this->prop['address']['pk'])?'null':$this->prop['address']['pk']).'';
                if (!empty($update))
                    $this->DB->query("UPDATE `group` SET ".implode(', ', $update)." WHERE pk = ".$this->prop['pk']);
            }
            $this->prop['name'] = trim($post['name']);
            $this->prop['phone'] = trim($post['phone']);
            $this->prop['is_family'] = $post['is_family'];

            $personsToRemove = $this->persons;
            $newPersons = [];
            foreach ($post['persons'] as $k => $row) {
                $oldPerson = empty($row['pk']) || !isset($this->persons[$row['pk']]) ? null : $this->persons[$row['pk']];
                $row = \AccueilALaFerme\User::save($this->DB, $row, $oldPerson);
                if (empty($oldPerson['pk'])) {
                    // Ajouter lien personne / groupe
                    $this->DB->query(
                            "INSERT INTO `person_has_group` (`group_id`, `person_id`, `group_link_pk`, `can_manage`, `event_pk`, `update_date`)
                            VALUES (:group_id, :person_id, :group_link_pk, :can_manage, :event_pk, NOW())", [
                                'group_id' => $this->prop['pk'],
                                'person_id' => $row['pk'],
                                'group_link_pk' => $row['group_link_pk'],
                                'can_manage' => $row['can_manage'],
                                'event_pk' => $event_pk
                            ]);
                } else {
                    $update = [];
                    if ($row['group_link_pk']*1 !== $oldPerson['group_link_pk']*1)
                        $update['group_link_pk'] = 'group_link_pk = '.$row['group_link_pk'].'';
                    if ($row['can_manage']*1 !== $oldPerson['can_manage']*1)
                        $update['can_manage'] = 'can_manage = '.$row['can_manage'].'';
                    if ($event_pk !== $oldPerson['event_pk'])
                        $update['event_pk'] = 'event_pk = '.$event_pk.'';
                    if (!empty($update)) {
                        $this->DB->query("UPDATE `person_has_group` SET ".implode(', ', $update).", update_date=now() WHERE group_id = ".$this->prop['pk']." AND person_id = ".$row['pk']);
                    }
                    unset($personsToRemove[$row['pk']]);
                }
                $newPersons[$row['pk']] = $row;
            }
            if (!empty($personsToRemove))
                $this->DB->query('UPDATE `person_has_group` SET was_removed=1, update_date=now() WHERE group_id = '.$this->prop['pk']." AND person_id IN (".implode(',', array_keys($personsToRemove)).")");
            $this->persons = $newPersons;
        }
    }
    public function validateData($post) {
        $post['is_family'] = 1*$post['is_family'];
        $post['name'] = trim($post['name']);
        $post['phone'] = trim($post['phone']);
        $errors = ['other' => [], 'persons' => []];
        if (empty($post['persons']))
            $errors['other'] = 'Veuillez entrer les membres de votre '.($post['is_family']? 'famille' : 'groupe');
        else {
            foreach ($post['persons'] as $k => $row) {
                $row['can_manage'] = empty($row['can_manage']) || $row['can_manage']=='false'? 0 : 1 ;
                $error = [];
                global $curPerson;
                if (!empty($row['pk']) && ($curPerson->data['pk'] != $row['pk'] && !isset($this->persons[$row['pk']])))
                    $error['pk'] = 'id personne inconnue';
                if (empty($row['link']) || !in_array($row['link'], array_keys($this->groupLinks)))
                    $error['link'] = 'Lien au groupe invalide';
                else $row['group_link_pk'] = $this->groupLinks[$row['link']]['pk']*1;
                if (empty($row['firstname']))
                    $error['firstname'] = 'champ vide';
                else $row['firstname'] = ucfirst($row['firstname']);
                if (empty($row['lastname']))
                    $error['lastname'] = 'champ vide';
                else $row['lastname'] = ucfirst($row['lastname']);
                if (empty($row['birthday']) && in_array($row['link'], ['fils', 'fille', 'friend_boy', 'friend_girl']))
                    $error['birthday'] = 'age enfant vide';
                else if (!empty($row['birthday']) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $row['birthday']))
                    $error['birthday'] = "mauvais format date d'anniversaire";
                if (!empty($error)) {
                    if ( empty($errors['other']['persons_errors']))
                        $errors['other']['persons_errors'] = [];
                    $errors['other']['persons_errors'] = $errors['other']['persons_errors'] + array_count_values($error);
                    $errors['persons'][$k] = $error;
                }
                $post['persons'][$k]=$row;
            }
        }
        if (empty($errors['other']) && empty($errors['persons']))
            $errors = null;
        return ['errors'=>$errors, 'post'=>$post];
    }

    public static function getBasicFields($curPerson=null) {
        if (!empty($curPerson)) {
            $d = $curPerson->data;
            $d['can_manage'] = true;
        } else
            $d = [ 'pk' => '', 'can_manage' => false, 'firstname' => '', 'lastname' => '', 'email' => '', 'phone' => '', 'birthday' => '' ];
        return [
            'prop' => [
                'pk' => '', 'is_family' => '1', 'name' => $d['lastname'], 'phone' => '',
                'address' => \AccueilALaFerme\Address::nullAddress()
            ],
            'persons' => [ [
                'pk' => $d['pk'], 'can_manage' => $d['can_manage'], 'link' => 'pere',
                'firstname' => $d['firstname'], 'lastname' => $d['lastname'], 'email' => $d['email'], 'phone' => $d['phone'], 'birthday' => $d['birthday']
            ] ]
        ];
    }
}