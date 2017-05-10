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

    function __construct($pk, $DB) {
        $this->DB = $DB;
        $this->prop = null;
        if (!empty($pk))
            $this->prop = $DB->queryFirst("SELECT g.*, a.pk addr_pk, a.street addr_street, a.postal_code addr_postal_code, a.city addr_city, a.region addr_region, a.country addr_country, e.name event_name, e.slug event_slug
                                            FROM group g
                                                LEFT JOIN address a ON a.pk = g.address_pk
                                                LEFT JOIN event e ON e.pk = g.event_pk
                                            WHERE pk = :pk", ['pk' => $pk]);
        if (empty($this->prop))
            throw new Exception("unkown group");
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

    public static function getBasicFields($curPerson=null) {
        if (!empty($curPerson))
            $d = $curPerson->data;
        else
            $d = [ 'pk' => '', 'firstname' => '', 'lastname' => '', 'email' => '', 'phone' => '', 'birthday' => '' ];
        return [
            'prop' => [
                'is_family' => '1', 'name' => $d['lastname'], 'phone' => '',
                'address' => ['pk' => null, 'street' => '', 'postal_code' => '', 'city' => '', 'region' => '', 'country' => '']
            ],
            'persons' => [ [
                'pk' => $d['pk'], 'can_manage' => true, 'link' => 'pere',
                'firstname' => $d['firstname'], 'lastname' => $d['lastname'], 'email' => $d['email'], 'phone' => $d['phone'], 'birthday' => $d['birthday']
            ] ]
        ];
    }
}