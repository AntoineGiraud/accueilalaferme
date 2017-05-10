<?php

namespace AccueilALaFerme;

/**
* classe chargeant la famille d'un utilisateur
*/
class User {
    public $data;

    function __construct($DB, $pk, $email, $firstname=null, $lastname=null, $birthday=null, $phone=null, $address=[], $comment=null) {
        $this->DB = $DB;
        if (empty($pk) && empty($email))
            throw new \Exception("No person email or id provided", 1);
        else if (!empty($pk)) {
            $this->data = $DB->queryFirst("SELECT * FROM person WHERE pk = :pk", ['pk' => $pk]);
            if (empty($this->data))
                throw new \Exception("unkown person id", 1);
        } else
            $this->data = $DB->queryFirst("SELECT * FROM person WHERE email = :email", ['email' => $email]);
        if (empty($this->data)) {
            $userId = $DB->query('INSERT INTO person (firstname, lastname, email, phone, birthday, comment)
                                  VALUES (:firstname, :lastname, :email, :phone, :birthday, "ajouté via admin wordpress...")',
                                ['firstname'=>$firstname, 'lastname'=>$lastname, 'email'=>$email, 'phone'=>$phone, 'birthday'=>$birthday]);
            $this->data = [
                'pk' => $userId,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'birthday' => $birthday,
                'phone' => $phone,
                'address_pk' => null,
                'comment' => null
            ];
        } else { // avons nous une MAJ ?
            $update = [];
            if ($this->data['email'] !== $email) $update['email'] = $email;
            if ($firstname !== null && $this->data['firstname'] != $firstname) $update['firstname'] = $firstname;
            if ($lastname !== null && $this->data['lastname'] != $lastname) $update['lastname'] = $lastname;
            if ($birthday !== null && $this->data['birthday'] != $birthday) $update['birthday'] = $birthday;
            if ($phone !== null && $this->data['phone'] != $phone) $update['phone'] = $phone;
            if (!empty($update))
                $DB->query("UPDATE person SET ".implode(', ', array_map(function($d){return $d.'=:'.$d;}, array_keys($update)))." WHERE pk = ".$this->data['pk'], $update);
        }
        $this->loadGroups();
    }

    public function loadGroups() {
        $res = $this->DB->query("SELECT g.*, phg.can_manage, phg.group_link_pk, phg.event_pk
                    FROM `group` g
                        LEFT JOIN person_has_group phg ON phg.group_id = g.pk
                    WHERE phg.person_id = :person_id AND phg.was_removed = 0 AND g.was_canceled = 0",
                    ['person_id'=>$this->data['pk']]);
        $this->canManageGroupIds = [];
        $this->groups = [];
        foreach ($res as $row) {
            $this->canManageGroupIds = $row['pk'];
            $this->groups[$row['pk']] = $row;
        }
    }

    public static function getUserId($DB, $email) {
        $res = $DB->queryFirst('SELECT id FROM person WHERE email = :email', ['email'=>$email]);
        return !empty($res)? null : current($res);
    }
    public static function getUserEmail($DB, $id) {
        $res = $DB->queryFirst('SELECT id FROM person WHERE email = :email', ['email'=>$email]);
        return !empty($res)? null : current($res);
    }
}