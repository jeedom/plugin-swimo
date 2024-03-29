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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
	<div class="form-group">
		<label class="col-sm-3 control-label">{{Type de connexion}}</label>
		<div class="col-xs-11 col-sm-7">
			<select id="connectType" class="configKey form-control" data-l1key="connectType" >
				<option value="local">Local</option>
				<option value="cloud">Cloud</option>
			</select>
		</div>
	</div>
        <div id="ipaddress" class="form-group">
            <label class="col-sm-3 control-label">{{Adresse IP}}</label>
            <div class="col-xs-11 col-sm-7">
                <input  class="configKey form-control" data-l1key="ipaddress" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{Serial}}</label>
            <div class="col-xs-11 col-sm-7">
                <input class="configKey form-control" data-l1key="serial" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">{{Apikey}}</label>
            <div class="col-xs-11 col-sm-7">
                <input class="configKey form-control" data-l1key="apikey" />
            </div>
        </div>
  </fieldset>
</form>
<script>
$('#connectType').on('select', function () {
	if($('#connectType option:selected').val() == "cloud"){
$('#connectType').hide();
}
if($('#connectType option:selected').val() == "local"){
$('#connectType').show();
}
});
</script>
