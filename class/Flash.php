<?php

namespace AccueilALaFerme;
class Flash {

    /**
     *
     * @param <type> $message
     * @param <type> $type
     */
    static function setFlash($message, $type='success'){ // On créer un tableau dans lequel on stock un message et un type qu'on place dans la variable flash de la variable $_session
        if (!isset($_SESSION['flash']))
            $_SESSION['flash'] = [];
        $_SESSION['flash'][] = [
            'msg' => $message,
            'type' => $type
        ];
    }

    /**
     *
     * @return string
     */
    static function flash(){ //parcourir dans les flash de la $_session, le array contenant le message défini grâce au setflash
        if (isset($_SESSION['flash'])) {
            $html = '';
            foreach ($_SESSION['flash'] as $k => $v) {
                if (isset($v['msg']))
                    $html .= '<p class="alert alert-'.$v['type'].'"><button class="close" data-dismiss="alert">×</button>'.$v['msg'].'</p>';
            }
            $_SESSION['flash'] = [];
            return $html;
        }
    }
}