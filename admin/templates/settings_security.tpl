<h3>{$SystemVersion} - {i18n('Settings')} &raquo; {i18n('Security')}</h3>
{if $message}<div class="{$class}">{$message}</div>{/if}

<form action="{$URL_REQUEST}" method="post" id="This">
	<h4>{i18n('Flood settings')}</h4>
	<div class="tbl1">
		<div class="grid_6 formLabel"><label for="FloodInterval">{i18n('Flood interval (seconds):')}</label></div>
		<div class="grid_4 formField"><input type="text" name="flood_interval" value="{$flood_interval}" id="FloodInterval" class="num_2" maxlength="2" /></div>
	</div>
	<div class="tbl2">
		<div class="grid_6 formLabel">{i18n('Flood auto ban:')}</div>
		<div class="grid_1 formField"><label><input type="radio" name="flood_autoban" value="1"{if $flood_autoban == 1} checked="checked"{/if} /> {i18n('Yes')}</label></div>
		<div class="grid_5 formField"><label><input type="radio" name="flood_autoban" value="0"{if $flood_autoban == 0} checked="checked"{/if} /> {i18n('No')}</label></div>
	</div>
	<h4>{i18n('Bad Words')}</h4>
	<div class="tbl1">
		<div class="grid_6 formLabel">{i18n('Bad words filter enabled:')}</div>
		<div class="grid_1 formField"><label><input type="radio" name="bad_words_enabled" value="1"{if $bad_words_enabled == 1} checked="checked"{/if} /> {i18n('Yes')}</label></div>
		<div class="grid_5 formField"><label><input type="radio" name="bad_words_enabled" value="0"{if $bad_words_enabled == 0} checked="checked"{/if} /> {i18n('No')}</label></div>
	</div>
	<div class="tbl2">
		<div class="grid_6 formLabel">
			<label for="BadWords">{i18n('Bad words list:')}</label>
			<small>{i18n('Enter one word per line.')}</small>
			<small>{i18n('Leave empty if not required.')}</small>
		</div>
		<div class="grid_4 formField"><textarea name="bad_words" id="BadWords" class="resize" cols="80" rows="3">{$bad_words}</textarea></div>
	</div>
	<div class="tbl1">
		<div class="grid_6 formLabel"><label for="BadWordReplace">{i18n('Bad word replacement:')}</label></div>
		<div class="grid_4 formField"><input type="text" name="bad_word_replace" value="{$bad_word_replace}" id="BadWordReplace" class="num_128" maxlength="128" /></div>
	</div>
	<h4>{i18n('Maintenance mode')}</h4>
	<div class="tbl1">
		<div class="grid_6 formLabel"><label for="MaintenanceLevel">{i18n('Maintenance level:')}</label></div>
		<div class="grid_6 formField">
			<select name="maintenance_level[]" multiple id="maintenance_level" class="select-multi" size="5">
				{section=maintenance_level}
					<option value="{$maintenance_level.value}"{if $maintenance_level.selected} selected="selected"{/if}>{$maintenance_level.display}</option>
				{/section}
			</select>
		</div>
	</div>
	<div class="tbl2">
		<div class="grid_6 formLabel">{i18n('Maintenance mode enabled:')}</div>
		<div class="grid_1 formField"><label><input type="radio" name="maintenance" value="1"{if $maintenance == 1} checked="checked"{/if} /> {i18n('Yes')}</label></div>
		<div class="grid_5 formField"><label><input type="radio" name="maintenance" value="0"{if $maintenance == 0} checked="checked"{/if} /> {i18n('No')}</label></div>
	</div>
	<div class="tbl1">
		<div class="grid_6 formLabel"><label for="MaintenanceMessage">{i18n('Maintenance mode message:')}</label><small>{i18n('HTML code is allowed.')}</small></div>
		<div class="grid_4 formField"><textarea name="MaintenanceMessage" id="MaintenanceMessage" class="resize" cols="80" rows="3">{$MaintenanceMessage}</textarea></div>
	</div>
	<div class="tbl AdminButtons">
		<div class="grid_2 center button-l">
			<span class="Cancel"><strong>{i18n('Back')}<img src="{$ADDR_ADMIN_ICONS}pixel/undo.png" alt="{i18n('Back')}" /></strong></span>
		</div>
		<div class="grid_2 center button-r">
			<input type="hidden" name="save" value="yes" />
			<span id="SendForm_This" class="Save"><strong>{i18n('Save')}<img src="{$ADDR_ADMIN_ICONS}pixel/diskette.png" alt="{i18n('Save')}" /></strong></span>
		</div>
	</div>
</form>