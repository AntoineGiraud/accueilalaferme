<?php

if (!is_admin() && !current_user_can('administrator'))
    \AccueilALaFerme\Flash::setFlashAndRedirect("Espace réservé à l'administration", 'warning', 'profil');

if(isset($_GET['event_id'])) {
    $event_id=$_GET['event_id'];
    $event = $DB->queryFirst("SELECT * FROM event WHERE pk = :pk", ['pk'=>$event_id]);
    if (empty($event))
        \AccueilALaFerme\Flash::setFlashAndRedirect("Evénement inconnu", 'warning', 'user_list');
    else if (strtotime($event['end_date']) < time())
        \AccueilALaFerme\Flash::setFlashAndRedirect("Evénement terminé", 'warning', 'user_list');
    $users = $DB->query("SELECT p.*, phg.was_removed, g.name, g.is_family FROM person p LEFT JOIN person_has_group phg ON p.pk=phg.person_id LEFT JOIN groupe g ON phg.group_id=g.pk LEFT JOIN registration r ON r.person_id=p.pk WHERE p.pk NOT IN (SELECT person_id FROM registration WHERE event_id=:event_id) ", ['event_id'=>$event_id]);
}
else {
    $users = $DB->query("SELECT p.*, phg.was_removed, g.pk group_id, g.name group_name, g.is_family FROM person p LEFT JOIN person_has_group phg ON p.pk=phg.person_id LEFT JOIN groupe g ON phg.group_id=g.pk");
}

get_header();
do_action('sydney_before_content');
?>

<div ng-app="app" ng-controller="UserListCtrl">
    <div id="primary" class="content-area fullwidth" ng-controller="PageCtrl">
    <h2><?= isset($_GET['event_id']) ? "Liste des utilisateurs du site ne s'étant pas inscris à : " . $event['name'] : "Liste des utilisateurs du site"?> ({{count_users}} utilisateurs)</h2>

        <div class="input-group">
          <span class="input-group-addon" id="basic-addon1">Recherche</span>
          <input type="text" ng-model='recherche' class="form-control" placeholder="Nom" aria-describedby="basic-addon1">
        </div>
        <br><br>

        <table class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Anniversaire</th>
                    <th>Groupe / Famille</th>
                    <th><?= isset($_GET['event_id']) ? "Aller à la page d'incription" : "Editer le profil" ?></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="user in users | filter:recherche">
                    <td>{{user.firstname}}</td>
                    <td>{{user.lastname}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.phone}}</td>
                    <td>{{user.birthday}} ({{user.age}})</td>
                    <td><a style="color:black" href="famille?group_id={{user.group_id}}">{{user.was_removed == null ? user.is_family == 1 ? "Famille " : user.is_family==0 ? "Groupe " : "" : ""}}</a></td>
                    <td><a style="color:black" href="profil?user_id={{user.pk}}"><span class="glyphicon glyphicon-pencil"></span></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    <?php
      global $js_for_layout;
      $js_for_layout = [
        'var users = ' . json_encode(array_values(array_map(function($d){
            $d['age']=getAge($d['birthday']);
            return $d;
        }, $users))) . ';',
        'angularjs',
        'angularjs_accueilalaferme/app.js',
        'angularjs_accueilalaferme/controllers/PageCtrl.js',
        'angularjs_accueilalaferme/controllers/UserListCtrl.js'
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>
