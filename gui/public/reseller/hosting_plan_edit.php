<?php
/**
 * i-MSCP a internet Multi Server Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010 by i-msCP | http://i-mscp.net
 * @version 	SVN: $Id$
 * @link 		http://i-mscp.net
 * @author 		ispCP Team
 * @author 		i-MSCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 * Portions created by the i-MSCP Team are Copyright (C) 2010 by
 * i-MSCP a internet Multi Server Control Panel. All Rights Reserved.
 */

require 'include/imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onResellerScriptStart);

check_login(__FILE__);

/**
 * @var $cfg iMSCP_Config_Handler_File
 */
$cfg = iMSCP_Registry::get('config');

$tpl = new iMSCP_pTemplate();
$tpl->define_dynamic('page', $cfg->RESELLER_TEMPLATE_PATH . '/hosting_plan_edit.tpl');
$tpl->define_dynamic('page_message', 'page');
$tpl->define_dynamic('logged_from', 'page');
$tpl->define_dynamic('subdomain_edit', 'page');
$tpl->define_dynamic('alias_edit', 'page');
$tpl->define_dynamic('mail_edit', 'page');
$tpl->define_dynamic('ftp_edit', 'page');
$tpl->define_dynamic('sql_db_edit', 'page');
$tpl->define_dynamic('sql_user_edit', 'page');
$tpl->define_dynamic('t_software_support', 'page');

/**
 * static page messages.
 */

global $hpid;

// Show main menu
gen_reseller_mainmenu($tpl, $cfg->RESELLER_TEMPLATE_PATH . '/main_menu_hosting_plan.tpl');
gen_reseller_menu($tpl, $cfg->RESELLER_TEMPLATE_PATH . '/menu_hosting_plan.tpl');

gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_RESELLER_MAIN_INDEX_PAGE_TITLE'	=> tr('i-MSCP - Reseller/Edit hosting plan'),
		'THEME_COLOR_PATH' => "../themes/{$cfg->USER_INITIAL_THEME}",
		'THEME_CHARSET' => tr('encoding'),
		'ISP_LOGO' => get_logo($_SESSION['user_id'])
	)
);

$tpl->assign(
	array(
		'TR_HOSTING PLAN PROPS' 	=> tr('Hosting plan properties'),
		'TR_TEMPLATE_NAME' 			=> tr('Template name'),
		'TR_MAX_SUBDOMAINS' 		=> tr('Max subdomains<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_ALIASES' 			=> tr('Max aliases<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_MAILACCOUNTS' 		=> tr('Mail accounts limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_FTP' 				=> tr('FTP accounts limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_SQL' 				=> tr('SQL databases limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_SQL_USERS' 			=> tr('SQL users limit<br><i>(-1 disabled, 0 unlimited)</i>'),
		'TR_MAX_TRAFFIC' 			=> tr('Traffic limit [MB]<br><i>(0 unlimited)</i>'),
		'TR_DISK_LIMIT' 			=> tr('Disk limit [MB]<br><i>(0 unlimited)</i>'),
		'TR_SOFTWARE_SUPP'			=> tr('i-MSCP application installer'),
		'TR_PHP' 					=> tr('PHP'),
		'TR_CGI' 					=> tr('CGI / Perl'),
		'TR_DNS' 					=> tr('Allow adding records to DNS zone (EXPERIMENTAL)'),
		'TR_BACKUP' 				=> tr('Backup'),
		'TR_BACKUP_DOMAIN' 			=> tr('Domain'),
		'TR_BACKUP_SQL' 			=> tr('SQL'),
		'TR_BACKUP_FULL' 			=> tr('Full'),
		'TR_BACKUP_NO' 				=> tr('No'),
		'TR_APACHE_LOGS' 			=> tr('Apache logfiles'),
		'TR_AWSTATS' 				=> tr('AwStats'),
		'TR_YES' 					=> tr('yes'),
		'TR_NO' 					=> tr('no'),
		'TR_BILLING_PROPS' 			=> tr('Billing Settings'),
		'TR_PRICE' 					=> tr('Price'),
		'TR_SETUP_FEE' 				=> tr('Setup fee'),
		'TR_VALUE' 					=> tr('Currency'),
		'TR_PAYMENT' 				=> tr('Payment period'),
		'TR_STATUS'					=> tr('Available for purchasing'),
		'TR_TEMPLATE_DESCRIPTON' 	=> tr('Description'),
		'TR_EXAMPLE' 				=> tr('(e.g. EUR)'),
		'TR_TOS_PROPS' 				=> tr('Term Of Service'),
		'TR_TOS_NOTE' 				=> tr('<b>Optional:</b> Leave this field empty if you do not want term of service for this hosting plan.'),
		'TR_TOS_DESCRIPTION' 		=> tr('Text'),
		'TR_EDIT_HOSTING_PLAN' 		=> tr('Update plan'),
		'TR_UPDATE_PLAN' 			=> tr('Update plan'),
		'HOSTING_PLAN_ID' 			=> $hpid
	)
);

/**
 * Dynamic page process
 */

if (isset($_POST['uaction']) && ('add_plan' == $_POST['uaction'])) {
	// Process data
	if (check_data_iscorrect($tpl)) { // Save data to db
		save_data_to_db();
	} else {
		restore_form($tpl);
	}
} else {
	// Get hosting plan id that comes for edit
	if (isset($_GET['hpid'])) {
		$hpid = $_GET['hpid'];
	}

	gen_load_ehp_page($tpl, $hpid, $_SESSION['user_id']);
	$tpl->assign('MESSAGE', '');
}

if (isset($cfg->HOSTING_PLANS_LEVEL) && $cfg->HOSTING_PLANS_LEVEL === 'reseller') {
    get_reseller_software_permission($tpl, $_SESSION['user_id']);
}
	
generatePageMessage($tpl);

$tpl->parse('PAGE', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(
    iMSCP_Events::onResellerScriptEnd, new iMSCP_Events_Response($tpl));

$tpl->prnt();

/**
 * Function definitions
 */

/**
 * Restore form on any error
 */
function restore_form($tpl) {

	/**
	 * @var $cfg iMSCP_Config_Handler_File
	 */
	$cfg = iMSCP_Registry::get('config');

	$tpl->assign(
		array(
			'HP_NAME_VALUE'			=> clean_input($_POST['hp_name'], true),
			'HP_DESCRIPTION_VALUE'	=> clean_input($_POST['hp_description'], true),
			'TR_MAX_SUB_LIMITS'		=> clean_input($_POST['hp_sub'], true),
			'TR_MAX_ALS_VALUES' 	=> clean_input($_POST['hp_als'], true),
			'HP_MAIL_VALUE' 		=> clean_input($_POST['hp_mail'], true),
			'HP_FTP_VALUE' 			=> clean_input($_POST['hp_ftp'], true),
			'HP_SQL_DB_VALUE' 		=> clean_input($_POST['hp_sql_db'], true),
			'HP_SQL_USER_VALUE' 	=> clean_input($_POST['hp_sql_user'], true),
			'HP_TRAFF_VALUE' 		=> clean_input($_POST['hp_traff'], true),
			'HP_TRAFF' 				=> clean_input($_POST['hp_traff'], true),
			'HP_DISK_VALUE' 		=> clean_input($_POST['hp_disk'], true),
			'HP_PRICE' 				=> clean_input($_POST['hp_price'], true),
			'HP_SETUPFEE' 			=> clean_input($_POST['hp_setupfee'], true),
			'HP_CURRENCY' 			=> clean_input($_POST['hp_currency'], true),
			'HP_PAYMENT' 			=> clean_input($_POST['hp_payment'], true),
			'HP_TOS_VALUE' 			=> clean_input($_POST['hp_tos'], true),
			'TR_PHP_YES' 			=> ($_POST['php'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_PHP_NO' 			=> ($_POST['php'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_YES' 			=> ($_POST['cgi'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_NO' 			=> ($_POST['cgi'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_YES' 			=> ($_POST['dns'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_NO' 			=> ($_POST['dns'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPD' 			=> ($_POST['backup'] == '_dmn_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPS' 			=> ($_POST['backup'] == '_sql_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPF' 			=> ($_POST['backup'] == '_full_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPN' 			=> ($_POST['backup']== '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_YES' 		=> ($_POST['status']) ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_NO' 			=> (!$_POST['status']) ? $cfg->HTML_CHECKED : '',
			'TR_SOFTWARE_YES' 		=> ($_POST['software_allowed'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_SOFTWARE_NO' 		=> ($_POST['software_allowed'] == '_no_') ? $cfg->HTML_CHECKED : '',
		)
	);
} // end of function restore_form()

/**
 * Generate load data from sql for requested hosting plan
 */
function gen_load_ehp_page($tpl, $hpid, $admin_id) {

	/**
	 * @var $cfg iMSCP_Config_Handler_File
	 */
	$cfg = iMSCP_Registry::get('config');

	$_SESSION['hpid'] = $hpid;

	if (isset($cfg->HOSTING_PLANS_LEVEL)
		&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
		$query = "
			SELECT
				*
			FROM
				`hosting_plans`
			WHERE
				`id` = ?
			;
		";

		$res = exec_query($query, $hpid);

		$readonly = $cfg->HTML_READONLY;
		$disabled = $cfg->HTML_DISABLED;
		$edit_hp = tr('View hosting plan');

		$tpl->assign('FORM', '');

	} else {
		$query = "
			SELECT
				*
			FROM
				`hosting_plans`
			WHERE
				`reseller_id` = ?
			AND
				`id` = ?
			;
		";

		$res = exec_query($query, array($admin_id, $hpid));
		$readonly = '';
		$disabled = '';
		$edit_hp = tr('Edit hosting plan');
	}

	if ($res->rowCount() !== 1) { // Error
		user_goto('hosting_plan.php');
	}

	$data = $res->fetchRow();

	$props = $data['props'];
	$description = $data['description'];
	$price = $data['price'];
	$setup_fee = $data['setup_fee'];
	$value = $data['value'];
	$payment = $data['payment'];
	$status = $data['status'];
	$tos = $data['tos'];

	list(
		$hp_php, $hp_cgi, $hp_sub, $hp_als, $hp_mail, $hp_ftp, $hp_sql_db,
		$hp_sql_user, $hp_traff, $hp_disk, $hp_backup, $hp_dns, $hp_allowsoftware
	) = explode(';', $props);

	$hp_name = $data['name'];

	if ($description == '')
		$description = '';

	if ($tos == '') {
		$tos = '';
	}

	if ($payment == '') {
		$payment = '';
	}

	if ($value == '') {
		$value = '';
	}

	list(
		$rsub_max,
		$rals_max,
		$rmail_max,
		$rftp_max,
		$rsql_db_max,
		$rsql_user_max
		) = check_reseller_permissions($_SESSION['user_id'], 'all_permissions');

	if ($rsub_max == "-1") $tpl->assign('ALIAS_EDIT', '');
	if ($rals_max == "-1") $tpl->assign('SUBDOMAIN_EDIT', '');
	if ($rmail_max == "-1") $tpl->assign('MAIL_EDIT', '');
	if ($rftp_max == "-1") $tpl->assign('FTP_EDIT', '');
	if ($rsql_db_max == "-1") $tpl->assign('SQL_DB_EDIT', '');
	if ($rsql_user_max == "-1") $tpl->assign('SQL_USER_EDIT', '');

	$tpl->assign(
		array(
			'HP_NAME_VALUE' 		=> tohtml($hp_name),
			'TR_EDIT_HOSTING_PLAN'	=> tohtml($edit_hp),
			'HOSTING_PLAN_ID' 		=> tohtml($hpid),
			'TR_MAX_SUB_LIMITS' 	=> tohtml($hp_sub),
			'TR_MAX_ALS_VALUES' 	=> tohtml($hp_als),
			'HP_MAIL_VALUE' 		=> tohtml($hp_mail),
			'HP_FTP_VALUE' 			=> tohtml($hp_ftp),
			'HP_SQL_DB_VALUE' 		=> tohtml($hp_sql_db),
			'HP_SQL_USER_VALUE' 	=> tohtml($hp_sql_user),
			'HP_TRAFF_VALUE' 		=> tohtml($hp_traff),
			'HP_DISK_VALUE' 		=> tohtml($hp_disk),
			'HP_DESCRIPTION_VALUE' 	=> tohtml($description),
			'HP_PRICE' 				=> tohtml($price),
			'HP_SETUPFEE' 			=> tohtml($setup_fee),
			'HP_CURRENCY' 			=> tohtml($value),
			'READONLY' 				=> tohtml($readonly),
			'DISBLED' 				=> tohtml($disabled),
			'HP_PAYMENT' 			=> tohtml($payment),
			'HP_TOS_VALUE' 			=> tohtml($tos),
			'TR_PHP_YES' 			=> ($hp_php == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_PHP_NO' 			=> ($hp_php == '_no_')	? $cfg->HTML_CHECKED : '',
			'TR_CGI_YES' 			=> ($hp_cgi == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_NO' 			=> ($hp_cgi == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_YES' 			=> ($hp_dns == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_NO' 			=> ($hp_dns == '_no_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPD' 			=> ($hp_backup == '_dmn_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPS' 			=> ($hp_backup == '_sql_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPF' 			=> ($hp_backup == '_full_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPN' 			=> ($hp_backup == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_YES' 		=> ($status) ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_NO' 			=> (!$status) ? $cfg->HTML_CHECKED : '',
			'TR_SOFTWARE_YES' 		=> ($hp_allowsoftware == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_SOFTWARE_NO' 		=> ($hp_allowsoftware == '_no_' || !$hp_allowsoftware) ? $cfg->HTML_CHECKED : ''
		)
	);
} // end of gen_load_ehp_page()

/**
 * Check correction of input data
 */
function check_data_iscorrect($tpl) {

	global $hp_name, $hp_php, $hp_cgi, $hp_sub, $hp_als, $hp_mail, $hp_ftp, $hp_sql_db, $hp_sql_user, $hp_traff,
		$hp_disk, $hpid, $price, $setup_fee, $hp_backup, $hp_dns, $hp_allowsoftware;

	$ahp_error = array();
	$hp_name = clean_input($_POST['hp_name']);
	$hp_sub = clean_input($_POST['hp_sub']);
	$hp_als = clean_input($_POST['hp_als']);
	$hp_mail = clean_input($_POST['hp_mail']);
	$hp_ftp = clean_input($_POST['hp_ftp']);
	$hp_sql_db = clean_input($_POST['hp_sql_db']);
	$hp_sql_user = clean_input($_POST['hp_sql_user']);
	$hp_traff = clean_input($_POST['hp_traff']);
	$hp_disk = clean_input($_POST['hp_disk']);
	$price = clean_input($_POST['hp_price']);
	$setup_fee = clean_input($_POST['hp_setupfee']);

	if (isset($_SESSION['hpid'])) {
		$hpid = $_SESSION['hpid'];
	} else {
		$ahp_error[] = tr('Undefined reference to data!');
	}

	// put hosting plan id into session value
	$_SESSION['hpid'] = $hpid;

	// Get values from previous page and check him correction
	if (isset($_POST['php'])) {
		$hp_php = $_POST['php'];
	}

	if (isset($_POST['cgi'])) {
		$hp_cgi = $_POST['cgi'];
	}

	if (isset($_POST['dns'])) {
		$hp_dns = $_POST['dns'];
	}

    if (isset($_POST['backup'])) {
    	$hp_backup = $_POST['backup'];
    }
    
    if (isset($_POST['software_allowed'])) {
    	$hp_allowsoftware = $_POST['software_allowed'];
    } else {
    	$hp_allowsoftware = "_no_";
    }
    
    if ($hp_php == "_no_" && $hp_allowsoftware == "_yes_") {
    	$ahp_error[] = tr('The i-MSCP application installer needs PHP to enable it!');
    }

    list(
		$rsub_max, $rals_max, $rmail_max, $rftp_max, $rsql_db_max, $rsql_user_max) = check_reseller_permissions(
		$_SESSION['user_id'], 'all_permissions'
	);

	if ($rsub_max == "-1") {
		$hp_sub = "-1";
	} elseif (!imscp_limit_check($hp_sub, -1)) {
		$ahp_error[] = tr('Incorrect subdomains limit!');
	}

	if ($rals_max == "-1") {
		$hp_als = "-1";
	} elseif (!imscp_limit_check($hp_als, -1)) {
		$ahp_error[] = tr('Incorrect aliases limit!');
	}

	if ($rmail_max == "-1") {
		$hp_mail = "-1";
	} elseif (!imscp_limit_check($hp_mail, -1)) {
		$ahp_error[] = tr('Incorrect mail accounts limit!');
	}

	if ($rftp_max == "-1") {
		$hp_ftp = "-1";
	} elseif (!imscp_limit_check($hp_ftp, -1)) {
		$ahp_error[] = tr('Incorrect FTP accounts limit!');
	}

	if ($rsql_db_max == "-1") {
		$hp_sql_db = "-1";
	} elseif (!imscp_limit_check($hp_sql_db, -1)) {
		$ahp_error[] = tr('Incorrect SQL users limit!');
	} else if ($hp_sql_db == -1 && $hp_sql_user != -1) {
		$ahp_error[] = tr('SQL databases limit is <i>disabled</i>!');
	}

	if ($rsql_user_max == "-1") {
		$hp_sql_user = "-1";
	} elseif (!imscp_limit_check($hp_sql_user, -1)) {
		$ahp_error[] = tr('Incorrect SQL databases limit!');
	} else if ($hp_sql_user == -1 && $hp_sql_db != -1) {
		$ahp_error[] = tr('SQL users limit is <i>disabled</i>!');
	}

	if (!imscp_limit_check($hp_traff, null)) {
		$ahp_error[] = tr('Incorrect traffic limit!');
	}

	if (!imscp_limit_check($hp_disk, null)) {
		$ahp_error[] = tr('Incorrect disk quota limit!');
	}

	if (!is_numeric($price)) {
		$ahp_error[] = tr('Price must be a number!');
	}

	if (!is_numeric($setup_fee)) {
		$ahp_error[] = tr('Setup fee must be a number!');
	}

	if (empty($ahp_error)) {
		$tpl->assign('MESSAGE', '');
		return true;
	} else {
		set_page_message(format_message($ahp_error));
		return false;
	}
} // end of check_data_iscorrect()

/**
 * Add new host plan to DB
 */
function save_data_to_db() {
	global $tpl, $hp_name, $hp_php, $hp_cgi, $hp_sub, $hp_als, $hp_mail, $hp_ftp, $hp_sql_db, $hp_sql_user, $hp_traff,
	$hp_disk, $hpid, $hp_backup, $hp_dns, $hp_allowsoftware;
//	global $tos;

	$err_msg = '';
	$description = clean_input($_POST['hp_description']);
	$price = clean_input($_POST['hp_price']);
	$setup_fee = clean_input($_POST['hp_setupfee']);
	$currency = clean_input($_POST['hp_currency']);
	$payment = clean_input($_POST['hp_payment']);
	$status = clean_input($_POST['status']);
	$tos = clean_input($_POST['hp_tos']);

	$hp_props = "$hp_php;$hp_cgi;$hp_sub;$hp_als;$hp_mail;$hp_ftp;$hp_sql_db;" .
		"$hp_sql_user;$hp_traff;$hp_disk;$hp_backup;$hp_dns;$hp_allowsoftware";

	$admin_id = $_SESSION['user_id'];

	if (reseller_limits_check($err_msg, $admin_id, $hpid, $hp_props)) {
		if (!empty($err_msg)) {
			set_page_message($err_msg);
			restore_form($tpl);
			return false;
		} else {
			$query = "
				UPDATE
					`hosting_plans`
				SET
					`name` = ?,
					`description` = ?,
					`props` = ?,
					`price` = ?,
					`setup_fee` = ?,
					`value` = ?,
					`payment` = ?,
					`status` = ?,
					`tos` = ?
				WHERE
					`id` = ?
				;
			";

			exec_query(
				$query,
				array(
					$hp_name, $description, $hp_props, $price, $setup_fee,
					$currency, $payment, $status, $tos, $hpid
				)
			);

			$_SESSION['hp_updated'] = '_yes_';
			user_goto('hosting_plan.php');
		}
	} else {
		set_page_message(
			tr("Hosting plan values exceed reseller maximum values!")
		);

		restore_form($tpl);
		return false;
	}
} // end of save_data_to_db()

exit;
