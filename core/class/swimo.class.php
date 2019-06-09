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
  foreach ($result["accueil_appareil"] as $device) {
    $eqLogic = eqLogic::byLogicalId(1000 + $device['nmAction'],'swimo');
    if(is_object($eqLogic)){
      log::add('swimo', 'debug', 'valeurs actionneur : ' . $device['isOff']);
      $state = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'state');
      if(is_object($state)){
        $state->event($device['isOff']);
      }
      $mode = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode');
      if(is_object($mode)){
        $mode->event($device['textDevice']);
      }
      $valeurConsigne = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'valeurConsigne');
      if(is_object($valeurConsigne)){
        $valeurConsigne->event($device['consigne']);
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
    switch ($sensor['sensorType']) {
      case '1':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',-10);
      $valeur->setConfiguration('maxValue',20);
      $valeur->setUnite('°C');
      break;

      case '2':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',30);
      $valeur->setUnite('mg/l');
      break;

      case '3':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',10);
      $valeur->setUnite('Bar');
      break;

      case '4':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',30);
      $valeur->setUnite('pt');
      break;

      case '5':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',3000);
      $valeur->setUnite('mV');
      break;

      case '6':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',20);
      $valeur->setUnite('mS/cm');
      break;

      case '7':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',3);
      $valeur->setUnite('mg/l');
      break;

      case '8':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',1000);
      $valeur->setUnite('mg/l');
      break;

      case '14':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',-5);
      $valeur->setConfiguration('maxValue',5);
      $valeur->setUnite('m');
      break;

      case '18':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',0);
      $valeur->setConfiguration('maxValue',100);
      $valeur->setUnite('rh%');
      break;

      case '17':
      case '19':
      $valeur->setSubType('numeric');
      $valeur->setConfiguration('minValue',-15);
      $valeur->setConfiguration('maxValue',55);
      $valeur->setUnite('°C');
      break;

      case '9':
      case '10':
      case '11':
      case '12':
      case '13':
      case '15':
      case '16':
      case '20':
      case '21':
      case '22':
      $valeur->setSubType('binary');
      break;

      default:
      // code...
      break;
    }
    $valeur->save();
  }
  foreach ($result["accueil_appareil"] as $device) {
    if($device['securite']<2){
      $eqLogic = eqLogic::byLogicalId(1000 + $device['nmAction'],'swimo');
      if(!is_object($eqLogic)){
        $eqLogic = new swimo();
        $eqLogic->setEqType_name('swimo');
        $eqLogic->setLogicalId(1000 + $device['nmAction']);
        $eqLogic->setName($device['nameAction']);
      }
      $eqLogic->setIsEnable(1);
      $eqLogic->setConfiguration('nmAction',$device['nmAction']);
      $eqLogic->setConfiguration('idActionType',$device['idActionType']);
      $eqLogic->save();
      log::add('swimo', 'debug', 'device : ' . $device['nameAction']);
      $state = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'state');
      if(!is_object($state)){
        $state = new swimoCmd();
        $state->setEqLogic_id($eqLogic->getId());
        $state->setLogicalId('state');
        $state->setName('etat');
        $state->setIsHistorized(1);
        $state->setIsVisible(1);
      }
      $state->setType('info');
      $state->setSubType('binary');
      $state->save();

      if($device['unitConsigne'] <> ""){
        $valeurConsigne = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'valeurConsigne');
        if(!is_object($valeurConsigne)){
          $valeurConsigne = new swimoCmd();
          $valeurConsigne->setEqLogic_id($eqLogic->getId());
          $valeurConsigne->setLogicalId('valeurConsigne');
          $valeurConsigne->setName('valeurConsigne');
          $valeurConsigne->setIsHistorized(0);
          $valeurConsigne->setIsVisible(0);
        }
        $valeurConsigne->setType('info');
        $valeurConsigne->setSubType('numeric');
        $valeurConsigne->save();

        $consigne = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne');
        if(!is_object($consigne)){
          $consigne = new swimoCmd();
          $consigne->setEqLogic_id($eqLogic->getId());
          $consigne->setLogicalId('consigne');
          $consigne->setName('consigne');
          $consigne->setIsHistorized(0);
          $consigne->setIsVisible(0);
          $consigne->setConfiguration('minValue',0);
          if($device['consigne'] > 100){
            $consigne->setConfiguration('maxValue',1000);
          }else{
            $consigne->setConfiguration('maxValue',100);
          }
        }
        $consigne->setConfiguration('type','con');
        $consigne->setType('action');
        $consigne->setSubType('slider');
        $consigne->setValue('valeurConsigne');
        $consigne->save();
      }

      $mode = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode');
      if(!is_object($mode)){
        $mode = new swimoCmd();
        $mode->setEqLogic_id($eqLogic->getId());
        $mode->setLogicalId('mode');
        $mode->setName('Mode');
        $mode->setIsHistorized(0);
        $mode->setIsVisible(1);
      }
      $mode->setType('info');
      $mode->setSubType('string');
      $mode->save();

      $modeOn = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'modeOn');
      if(!is_object($modeOn)){
        $modeOn = new swimoCmd();
        $modeOn->setEqLogic_id($eqLogic->getId());
        $modeOn->setLogicalId('modeOn');
        $modeOn->setName('On');
        $modeOn->setIsVisible(1);
      }
      $modeOn->setType('action');
      $modeOn->setSubType('other');
      $modeOn->setConfiguration('index',0);
      $modeOn->setConfiguration('type','index');
      $modeOn->save();

      $modeOff = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'modeOff');
      if(!is_object($modeOff)){
        $modeOff = new swimoCmd();
        $modeOff->setEqLogic_id($eqLogic->getId());
        $modeOff->setLogicalId('modeOff');
        $modeOff->setName('Off');
        $modeOff->setIsVisible(1);
      }
      $modeOff->setType('action');
      $modeOff->setSubType('other');
      $modeOff->setConfiguration('index',1);
      $modeOff->setConfiguration('type','index');
      $modeOff->save();

      $modeAuto = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'modeAuto');
      if(!is_object($modeAuto)){
        $modeAuto = new swimoCmd();
        $modeAuto->setEqLogic_id($eqLogic->getId());
        $modeAuto->setLogicalId('modeAuto');
        $modeAuto->setName('Auto');
        $modeAuto->setIsVisible(1);
      }
      $modeAuto->setType('action');
      $modeAuto->setSubType('other');
      $modeAuto->setConfiguration('index',2);
      $modeAuto->setConfiguration('type','index');
      $modeAuto->save();

      switch ($device['idActionType']) {
        case '1':
          $progPlage = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progPlage');
          if(!is_object($progPlage)){
            $progPlage = new swimoCmd();
            $progPlage->setEqLogic_id($eqLogic->getId());
            $progPlage->setLogicalId('progPlage');
            $progPlage->setName('Prog Plage');
            $progPlage->setIsVisible(1);
          }
          $progPlage->setType('action');
          $progPlage->setSubType('other');
          $progPlage->setConfiguration('prog',0);
          $progPlage->setConfiguration('type','prog');
          $progPlage->save();

          $progNight = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progNight');
          if(!is_object($progNight)){
            $progNight = new swimoCmd();
            $progNight->setEqLogic_id($eqLogic->getId());
            $progNight->setLogicalId('progNight');
            $progNight->setName('Prog Night');
            $progNight->setIsVisible(1);
          }
          $progNight->setType('action');
          $progNight->setSubType('other');
          $progNight->setConfiguration('prog',1);
          $progNight->setConfiguration('type','prog');
          $progNight->save();

          $progDay = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progDay');
          if(!is_object($progDay)){
            $progDay = new swimoCmd();
            $progDay->setEqLogic_id($eqLogic->getId());
            $progDay->setLogicalId('progDay');
            $progDay->setName('Prog Day');
            $progDay->setIsVisible(1);
          }
          $progDay->setType('action');
          $progDay->setSubType('other');
          $progDay->setConfiguration('prog',2);
          $progDay->setConfiguration('type','prog');
          $progDay->save();

          $progWinter = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progWinter');
          if(!is_object($progWinter)){
            $progWinter = new swimoCmd();
            $progWinter->setEqLogic_id($eqLogic->getId());
            $progWinter->setLogicalId('progWinter');
            $progWinter->setName('Prog Winter');
            $progWinter->setIsVisible(1);
          }
          $progWinter->setType('action');
          $progWinter->setSubType('other');
          $progWinter->setConfiguration('prog',3);
          $progWinter->setConfiguration('type','prog');
          $progWinter->save();
          break;

        case '2':
        case '7':
        case '13':
          $progPlage = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progPlage');
          if(!is_object($progPlage)){
            $progPlage = new swimoCmd();
            $progPlage->setEqLogic_id($eqLogic->getId());
            $progPlage->setLogicalId('progPlage');
            $progPlage->setName('Prog Plage');
            $progPlage->setIsVisible(1);
          }
          $progPlage->setType('action');
          $progPlage->setSubType('other');
          $progPlage->setConfiguration('prog',0);
          $progPlage->setConfiguration('type','prog');
          $progPlage->save();

          $progEco = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progEco');
          if(!is_object($progEco)){
            $progEco = new swimoCmd();
            $progEco->setEqLogic_id($eqLogic->getId());
            $progEco->setLogicalId('progEco');
            $progEco->setName('Prog Eco');
            $progEco->setIsVisible(1);
          }
          $progEco->setType('action');
          $progEco->setSubType('other');
          $progEco->setConfiguration('prog',1);
          $progEco->setConfiguration('type','prog');
          $progEco->save();

          $progMax = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'progMax');
          if(!is_object($progMax)){
            $progMax = new swimoCmd();
            $progMax->setEqLogic_id($eqLogic->getId());
            $progMax->setLogicalId('progMax');
            $progMax->setName('Prog Max');
            $progMax->setIsVisible(1);
          }
          $progMax->setType('action');
          $progMax->setSubType('other');
          $progMax->setConfiguration('prog',2);
          $progMax->setConfiguration('type','prog');
          $progMax->save();
          break;

        default:
        // code...
        break;
      }

      /*$valeur = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'value');
      if(!is_object($valeur)){
        $valeur = new swimoCmd();
        $valeur->setEqLogic_id($eqLogic->getId());
        $valeur->setLogicalId('value');
        $valeur->setName('valeur');
        $valeur->setIsHistorized(1);
        $valeur->setIsVisible(1);
      }
      $valeur->setType('info');
      switch ($device['idActionType']) {
        case '1':

        break;

        default:
        // code...
        break;
      }
      $valeur->save();*/
    }

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

  $ipaddress = config::byKey('ipaddress','swimo');
  $serial = config::byKey('serial','swimo');
  $apikey = config::byKey('apikey','swimo');
  $eqLogic = $this->getEqLogic();
  $nmAction = $eqLogic->getConfiguration('nmAction');
  $url = "http://".$ipaddress."/cgi-bin/updateDevice?serial=".$serial."&api=".$apikey."&nmAction=".$nmAction;
  $request_http = new com_http($url);
  if($this->getConfiguration('type') == 'index'){
    $url .= "&index=".$this->getConfiguration('index');
  }else if($this->getConfiguration('type') == 'prog'){
    $url .= "&codeSeq=".$this->getConfiguration('prog');
  }else if($this->getConfiguration('type') == 'con'){
    $url .= "&con=".$_options['slider'];
  }
    log::add('swimo', 'debug', 'url : '.$url);
    $request_http = new com_http($url);
    $request_http->exec(60,2);
    swimo::updateValues();

}

/*     * **********************Getteur Setteur*************************** */
}
