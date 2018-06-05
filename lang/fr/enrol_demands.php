<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Initially developped for :
 * Université de Cergy-Pontoise
 * 33, boulevard du Port
 * 95011 Cergy-Pontoise cedex
 * FRANCE
 *
 * Create the enrolment on demand method.
 *
 * @package   enrol_demands
 * @copyright 2018 Laurent Guillet <laurent.guillet@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : fr/enrol_demands.php
 * French language file
 */

$string['pluginname'] = "Demandes d'inscription";
$string['demands:config'] = "Configure l'inscription";
$string['demands:unenrol'] = "Peut désinscrire";
$string['demands:managecourseenrolment'] = "Gère l'inscription au cours";
$string['hasroleincourse'] = "Vous êtes déjà inscrit à ce cours";
$string['hasapplied'] = "Vous avez déjà postulé à ce cours";
$string['demandenrolment'] = "Postuler à ce cours";
$string['status'] = 'Autoriser les demandes d\'inscription';
$string['demandsmail'] = "Vous avez ".'{$a}'." demandes d'inscriptions en attente.
    <p>Pour y répondre, connectez-vous à CoursUCP
    (<a href='$CFG->wwwroot'>$CFG->wwwroot</a>)
    et consultez le bloc \"Demandes d'inscription\" sur votre tableau de bord. </p>
    <p>Vous pouvez aussi vous rendre directement sur la page
    <a href='$CFG->wwwroot/enrol/demands/requests.php'>
    $CFG->wwwroot/enrol/demands/requests.php</a>.</p>
    <p>Sur cette même page, vous pourrez également demander à ne plus recevoir de courriels
    comme celui-ci.</p>
    <p>Bien cordialement,<br>
    CoursUCP, votre plateforme pédagogique.</p>";
$string['succesfulenrolmentmail'] = "Bonjour, <br>"
        . "<br>Votre demande d'inscription au cours ".'{$a->coursename}'." vient d'être acceptée"
        . " par ".'{$a->userfirstname} {$a->userlastname} {$a->useremail}'.".<br>"
        . "Vous pouvez y accéder depuis $CFG->wwwroot --> onglet Mes cours.<br>"
        . "<br>Bon travail !<br>"
        . "CoursUCP, votre plateforme pédagogique<br>"
        . "Ceci est un message automatique. Merci de ne pas y répondre. "
        . "Pour toute demande ou information, nous vous invitons à "
        . "<a href='https://monucp.u-cergy.fr/uPortal/f/u312l1s6/p/"
        . "Assistance.u312l1n252/max/render.uP?pCp'>Effectuer une demande</a>"
        . " dans la catégorie <strong>SEFIAP -> Applications pédagogiques</strong>.";
$string['rejectedenrolmentmail'] = "Bonjour, <br>"
        . "<br>Votre demande d'inscription au cours ".'{$a->coursename} vient d\'être refusée '
        . 'par {$a->userfirstname} {$a->userlastname} {$a->useremail}'.".<br>"
        . "Nous vous conseillons : <br>"
        . "1 . De bien vérifier l'intitulé de ce cours : fait-il partie de votre cursus? <br>"
        . "2 . Si tout cela vous semble correct, contacter l'enseignant qui gère le cours."
        . "<br><br>Bien cordialement, <br>"
        . "CoursUCP, votre plateforme pédagogique<br>"
        . "Ceci est un message automatique. Merci de ne pas y répondre. "
        . "Pour toute demande ou information, nous vous invitons à "
        . "<a href='https://monucp.u-cergy.fr/uPortal/f/u312l1s6/p/"
        . "Assistance.u312l1n252/max/render.uP?pCp'>Effectuer une demande</a>"
        . " dans la catégorie <strong>SEFIAP -> Applications pédagogiques</strong>.";
$string['subjectaccepted'] = 'CoursUCP : Demande d\'inscription au cours {$a} acceptée';
$string['subjectrejected'] = 'CoursUCP : Demande d\'inscription au cours {$a} refusée';
$string['subjectreminder'] = "Vous avez des demandes d'inscription en attente";
$string['sendreminder'] = "Envoi des rappels de demande d'inscription hebdomadaire";
$string['headermanageenrolments'] = "<br><br>
    <h2>Demandes que vous avez reçues</h2>
    Des étudiants (ou des collègues) vous ont demandé de les inscrire à vos cours :<br><br>
    <a href='validate.php?all=1'><button class='btn btn-secondary'>Accepter tous</button></a>&nbsp;&nbsp;
    <a href='validate.php?all=2'><button class='btn btn-secondary'>Accepter tous si bonne VET</button></a><br><br>
    <a href='validate.php?all=3'><button class='btn btn-secondary'>Refuser tous</button></a>&nbsp;&nbsp;
    <a href='validate.php?all=4'><button class='btn btn-secondary'>Refuser tous si mauvaise VET</button></a><br><br>
    <br>";
$string['buttonstopreminders'] = "Courriels de rappels : "
        . "<a href='requests.php?nomail=1&tablenomail=reminder'>"
        . "<button class='btn btn-secondary'>Ne plus m'envoyer de courriel de rappel"
        . " pour ces demandes."
        . "</button></a>";
$string['buttonsendremindersagain'] = "Courriels de rappels : "
        . "<a href='requests.php?nomail=2&tablenomail=reminder'>"
        . "<button class='btn btn-secondary'>Merci de me signaler ces demandes"
        . " par courriel chaque lundi matin.</button></a>";
$string['buttonstopdemands'] = "Courriel à chaque nouvelle demande : "
        . "<a href='requests.php?nomail=1&tablenomail=demands'>"
        . "<button class='btn btn-secondary'>Ne plus m'envoyer de courriel pour les nouvelles"
        . " demandes."
        . "</button></a>";
$string['buttonsenddemandsagain'] = "Courriel à chaque nouvelle demande : "
        . "<a href='requests.php?nomail=2&tablenomail=demands'>"
        . "<button class='btn btn-secondary'>Merci de me signaler les nouvelles demandes"
        . " d'inscriptions.</button></a>";
$string['coursevet'] = "VET du cours";
$string['coursename'] = "Nom du cours";
$string['askedon'] = "Date de la demande";
$string['askedby'] = "Nom du demandeur";
$string['answeredon'] = "Date de la réponse";
$string['answeredby'] = "Nom de la personne ayant répondu";
$string['mailasker'] = "Mail du demandeur";
$string['vetasker'] = "VETs du demandeur";
$string['answer'] = "Réponse";
$string['accept'] = "Accepter";
$string['reject'] = "Refuser";
$string['headertableyourdemands'] = "<h2>Demandes que vous avez déposées</h2>
    <a href=/course/index.php><button class='btn btn-secondary' type=button>Ajouter une demande</button></a>
    <br><br>";
$string['waitingrequest'] = "Demandes en attente";
$string['acceptedrequest'] = "Demandes acceptées";
$string['rejectedrequest'] = "Demandes rejetées";
$string['subjectnewdemand'] = 'Nouvelle demande d\'inscription dans le cours {$a}';
$string['newdemandmail'] = "Vous avez une nouvelle demande d'inscription en attente pour le cours "
        . "".'{$a}'.".
    <p>Pour y répondre, connectez-vous à CoursUCP
    (<a href='$CFG->wwwroot'>$CFG->wwwroot</a>)
    et consultez le bloc \"Demandes d'inscription\" sur votre tableau de bord. </p>
    <p>Vous pouvez aussi vous rendre directement sur la page
    <a href='$CFG->wwwroot/enrol/demands/requests.php'>
    $CFG->wwwroot/enrol/demands/requests.php</a>.</p>
    <p>Sur cette même page, vous pourrez également demander à ne plus recevoir de courriels
    comme celui-ci.</p>
    <p>Bien cordialement,<br>
    CoursUCP, votre plateforme pédagogique.</p>";
$string['custommessage'] = "Message personnalisé";
$string['messageprovider:demands'] = "Nouvelle demande d'inscription reçue";
$string['messageprovider:enroled'] = "Demande d'inscription acceptée";
$string['messageprovider:rejected'] = "Demande d'inscription rejetée";
$string['messageprovider:reminder'] = "Demande d'inscription reçues";