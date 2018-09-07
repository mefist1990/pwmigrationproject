{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.jpg" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>Migration Project PW</h2>
			<h4>Модуль для миграции данных, между магазинами</h4>
		</div>
	</div>

	<hr />
<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>
					<h4>Версия магазина: {$ps_version}</h4>
					{if $empty_file == 0}<p class="bg-danger">В файле DB нет данных.</p>{/if}
					{if $empty_file == 1}<p class="bg-success">В файле DB есть данные.</p>{/if}
					<hr>
					{if !empty($information)}
						{$information}
					{/if}

				</p>

				<br />

				<p class="text-center">
					{if $empty_file == 0}<button type="submit" name="submitPwmigrationprojectModuleProductBackup" > Занести данные в DB </button>{/if}
					{if $empty_file == 1}<button type="submit" name="submitPwmigrationprojectModuleCheckingTables" > Произвести проверку таблиц на конфликты </button>
					{if $checking_tables > 0}
					<button type="submit" name="submitPwmigrationprojectModuleProductRestore" >Восстановить данные в Базу данных из DB</button>{/if}

					<button type="submit" name="submitPwmigrationprojectModuleDBReset" > Очистить DB </button>

					{/if}
				</p>
			</div>
		</div>
	</div>

</form>
</div>
