<?php

namespace AccueilALaFerme;

/**
* classe chargeant la famille d'un utilisateur
*/
class Address {
    public $data;

    public static function save($DB, $new, $old=null) {
        $allEmpty = true;
        foreach (['street', 'postal_code', 'city', 'region', 'country'] as $key) {
            $new[$key] = trim($new[$key]);
            if (!empty($new[$key]))
                $allEmpty = false;
        }
        if ($allEmpty)
            return self::nullAddress();
        if (empty($old['pk'])) { // Insert address
            $new['pk'] = $DB->query(
                "INSERT INTO `address` (`street`, `postal_code`, `city`, `region`, `country`)
                VALUES (:street, :postal_code, :city, :region, :country)", [
                    'street' => $new['street'],
                    'postal_code' => $new['postal_code'],
                    'city' => $new['city'],
                    'region' => $new['region'],
                    'country' => $new['country']
                ]);
        } else { // update address
            $update = [];
            if ($new['street'] !== $old['street']) $update['street'] = 'street = "'.$new['street'].'"';
            if ($new['postal_code'] !== $old['postal_code']) $update['postal_code'] = 'postal_code = "'.$new['postal_code'].'"';
            if ($new['city'] !== $old['city']) $update['city'] = 'city = "'.$new['city'].'"';
            if ($new['region'] !== $old['region']) $update['region'] = 'region = "'.$new['region'].'"';
            if ($new['country'] !== $old['country']) $update['country'] = 'country = "'.$new['country'].'"';
            if (!empty($update))
                $DB->query("UPDATE address SET ".implode(', ', $update)." WHERE pk = ".$old['pk']);
        }
        return $new;
    }
    public static function nullAddress() {
        return ['pk' => null, 'street' => '', 'postal_code' => '', 'city' => '', 'region' => '', 'country' => ''];
    }
}