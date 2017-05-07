<?php

namespace AccueilALaFerme;

/**
* classe chargeant la famille d'un utilisateur
*/
class Family {

    function __construct($email, $prenom, $nom) {
        global $DB;
        if (empty($email))
            throw new Exception("No email provided", 1);
        $user = $DB->queryFirst("SELECT * FROM person WHERE email = :email", ['email' => $email]);
        if (empty($user))
            $userId = $DB->query('INSERT INTO ');

    }
}