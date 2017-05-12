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

    function __construct($pk, $DB, $curPerson=null) {
        $this->DB = $DB;
        $this->prop = null;
        if ($pk === null) {
            $data = \AccueilALaFerme\Group::getBasicFields($curPerson);
            $this->prop = $data['prop'];
            $this->persons = $data['persons'];
            return $this;
        }

        if (!empty($pk))
            $this->prop = $DB->queryFirst("SELECT g.*, a.pk addr_pk, a.street addr_street, a.postal_code addr_postal_code, a.city addr_city, a.region addr_region, a.country addr_country, e.name event_name, e.slug event_slug
                                            FROM group g
                                                LEFT JOIN address a ON a.pk = g.address_pk
                                                LEFT JOIN event e ON e.pk = g.event_pk
                                            WHERE pk = :pk", ['pk' => $pk]);
        if (empty($this->prop))
            throw new Exception("unkown group");
        else
            $this->prop['address'] = [
                'pk' => $this->prop['addr_pk'], 'street' => $this->prop['addr_street'], 'postal_code' => $this->prop['addr_postal_code'], 'city' => $this->prop['addr_city'], 'region' => $this->prop['addr_region'], 'country' => $this->prop['addr_country']
            ];
            unset($this->prop['addr_pk'], $this->prop['addr_street'], $this->prop['addr_postal_code'], $this->prop['addr_city'], $this->prop['addr_region'], $this->prop['addr_country']);

            $res = $DB->query( "SELECT p.*, phg.can_manage, phg.group_link_pk, phg.was_removed, phg.event_pk
                                FROM person_has_group phg ON phg.person_id = p.pk
                                    LEFT JOIN person p
                                WHERE group_id = :group_id", ['group_id' => $group_id]);
            $this->persons = [];
            $this->admins = [];
            foreach ($res as $p) {
                if ($p['can_manage'])
                    $this->admins[] = $p['can_manage'];
                $this->persons[] = $p;

            }
    }

    public function saveFamily($post) {
        extract(self::validateData($post));
        if (!empty($errors)) {
            $this->errors = $errors;
            $this->persons = $post['persons'];
            foreach ($errors['persons'] as $key => $e)
                $this->persons[$key]['errors'] = $e;
            throw new \Exception("Des erreurs sont présentes dans le formulaire.", 1);
        } else {
            Flash::setFlash("on n'a plus qu'à enregistrer ce qui a été passé", "info");
        }
    }
    public static function validateData($post) {
        if (empty($post['is_family']))
            $post['is_family'] = 1;
        $errors = ['other' => [], 'persons' => []];
        if (empty($post['persons']))
            $errors['other'] = 'Veuillez entrer les membres de votre '.($post['is_family']? 'famille' : 'groupe');
        else {
            foreach ($post['persons'] as $k => $row) {
                $post['persons'][$k]['can_manage'] = !empty($row['can_manage']);
                $error = [];
                if (empty($row['firstname']))
                    $error['firstname'] = 'champ vide';
                else $post['persons'][$k]['firstname'] = ucfirst($row['firstname']);
                if (empty($row['lastname']))
                    $error['lastname'] = 'champ vide';
                else $post['persons'][$k]['lastname'] = ucfirst($row['lastname']);
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
                'address' => ['pk' => null, 'street' => '', 'postal_code' => '', 'city' => '', 'region' => '', 'country' => '']
            ],
            'persons' => [ [
                'pk' => $d['pk'], 'can_manage' => $d['can_manage'], 'link' => 'pere',
                'firstname' => $d['firstname'], 'lastname' => $d['lastname'], 'email' => $d['email'], 'phone' => $d['phone'], 'birthday' => $d['birthday']
            ] ]
        ];
    }
}