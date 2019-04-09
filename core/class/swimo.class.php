<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class swimo extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */


      public static function cron5() {
        swimo::updateValues();
      }



    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {

    }

    public function postInsert() {

    }

    public function preSave() {

    }

    public function postSave() {

    }

    public function preUpdate() {

    }

    public function postUpdate() {

    }

    public function preRemove() {

    }

    public function postRemove() {

    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */
     public static function updateValues(){
       $ipaddress = config::byKey('ipaddress','swimo');
       $serial = config::byKey('serial','swimo');
       $apikey = config::byKey('apikey','swimo');
       log::add('swimo', 'debug', 'start update ');
       $url = "http://".$ipaddress."/cgi-bin/getAll?serial=".$serial."&api=".$apikey;
       $request_http = new com_http($url);
       $result = json_decode($request_http->exec(60,2), true);
       foreach ($result["accueil_analyse"] as $sensor) {
         $eqLogic = eqLogic::byLogicalId($sensor['nmSensor'],'swimo');
         if(is_object($eqLogic)){
           $state = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'state');
           if(is_object($state)){
             $state->event($sensor['etatSensor']);
           }
           $value = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'value');
           if(is_object($value)){
             log::add('swimo', 'debug', 'valeur '. $sensor['liveSensor'][0]);
             $value->event($sensor['liveSensor'][0]);
           }
         }
       }
     }

     public static function sync(){
       log::add('swimo', 'debug', 'start sync ');
       $ipaddress = config::byKey('ipaddress','swimo');
       $serial = config::byKey('serial','swimo');
       $apikey = config::byKey('apikey','swimo');
       $url = "http://".$ipaddress."/cgi-bin/getAll?serial=".$serial."&api=".$apikey;
       $request_http = new com_http($url);
       $result = json_decode($request_http->exec(60,2), true);
       foreach ($result["accueil_analyse"] as $sensor) {
         $eqLogic = eqLogic::byLogicalId($sensor['nmSensor'],'swimo');
         if(!is_object($eqLogic)){
           $eqLogic = new swimo();
           $eqLogic->setEqType_name('swimo');
           $eqLogic->setLogicalId($sensor['nmSensor']);
           $eqLogic->setName($sensor['nameSensor']);
         }
         $eqLogic->setIsEnable(1);
         $eqLogic->setConfiguration('nmSensor',$sensor['nmSensor']);
         $eqLogic->setConfiguration('sensorType',$sensor['sensorType']);
         $eqLogic->save();
         log::add('swimo', 'debug', 'sensor : ' . $sensor['nameSensor']);
         $state = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'state');
         if(!is_object($state)){
           $state = new swimoCmd();
           $state->setEqLogic_id($eqLogic->getId());
           $state->setLogicalId('state');
           $state->setName('etat');
           $state->setIsHistorized(1);
           $state->setIsVisible(0);
         }
         $state->setType('info');
         $state->setSubType('binary');
         $state->save();

         $valeur = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'value');
         if(!is_object($valeur)){
           $valeur = new swimoCmd();
           $valeur->setEqLogic_id($eqLogic->getId());
           $valeur->setLogicalId('value');
           $valeur->setName('valeur');
           $valeur->setIsHistorized(1);
           $valeur->setIsVisible(1);
         }
         $valeur->setType('info');
         $valeur->setSubType('numeric');
         switch ($sensor['sensorType']) {
           case '1':
            $valeur->setConfiguration('minValue',$sensor['alarmMin']);
            $valeur->setConfiguration('maxValue',$sensor['alarmMax']);
            $valeur->setUnite('°C');
            break;

           default:
             // code...
             break;
         }
         $valeur->save();
       }
       swimo::updateValues();
     }
    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class swimoCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {

    }

    /*     * **********************Getteur Setteur*************************** */
}
