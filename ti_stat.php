<?php
/*
Plugin Name: TI Stat
Plugin URI: http://tigor.org.ua/ti-stat/
Description: Show Yandex.Metrika statistic on pages.
Version: DEV
Author: TIgor
Author URI: http://tigor.org.ua
License: GPL2
*/


/*  Copyright 2011 Tesliuk Igor  (email : tigor@tigor.org.ua)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



function admin_ti_stat_options(){

$tab = $_GET['tab'];


?>     <div class="wrap">

        <h2>TI Stat Options</h2>
		<div>
		
		Tabs : 
		<a href="?page=ti_stat">Settings</a> | 
		<a href="?page=ti_stat&tab=graphs">Graphs</a> | 
		<a href="?page=ti_stat&tab=traffic">Traffic</a> | 
		<a href="?page=ti_stat&tab=hourly">Hourly</a>
		
		
		</div>
        
		
		
		
		
		<form method="post" action="options.php">

            <?php settings_fields('ti_stat_group'); ?>

            <?php 
			$input = get_option('ti_stat');	
			$options = get_option('ti_stat_options');
			?>
			
			<?php
			
		require_once('Yauth.php');
		$auth = get_option('ti_yauth');
			
	if ('' == $tab) { 
			$args['title']='widget';
			//widget_ti_stat($args);
			
			
	
	
						
			
			
			if ($input['delete_token']=='true')
				{
				
				$auth->clear();
				$input['delete_token']='false';
				update_option('ti_yauth', $auth);
				}
			
			
			if (!$auth->check_token())
				{
				
				$auth->clear();
				$auth->get_token($input['username'],$input['password']);
				update_option('ti_yauth', $auth);
					if (!$auth->check_token())
					{
					echo $auth->name_error();
					}
				}
			$input['username'] = ''; // Username is NOT stored in database
			$input['password'] = ''; // Password is NOT stored in database
			
			update_option('ti_stat', $input);
			if ($input['days']!='')
				{
				$options['days']=$input['days'];
				}
			if ($input['life_time']!='')
				{
				$options['life_time']=$input['life_time'];
				}
			
			$options['today']=$input['today'];
			
			if ($options['schedule'] == '')
				{
				$options['schedule'] = '03';
				}
			
			if (($input['schedule'] != $options['schedule']) and ($input['schedule'] != '')) {
				$options['schedule'] = $input['schedule'];
				update_option('ti_stat_options',$options);
				$timestamp = wp_next_scheduled( 'ti_stat_daily_event' );
				wp_unschedule_event($timestamp, 'ti_stat_daily_event');
				wp_schedule_event(ti_stat_time(), 'daily', 'ti_stat_daily_event');
				}
			
			
			update_option('ti_stat_options',$options);
			
			if ($auth->check_token())
			{
			if (($auth->counter_id == '')and($input['counter']!=''))
				{
				$auth->counter_id = $input['counter'];
				update_option('ti_yauth', $auth);
				
				}
			?>
				
				<table class="form-table">
					<tr valign="top"><th scope="row">Days to include</th>
					
						<td><input type="TEXT" name="ti_stat[days]" value="<?php echo $options['days']; ?>" /></td>
						<td>Number of days that are used to build reports.</td>
					</tr>
					<tr valign="top"><th scope="row">Include todays stats</th>
					
						<td><input type="checkbox" value="true" name="ti_stat[today]" <?php if ($options['today']){echo 'CHECKED';}?> /></td>
						<td>Include statistic for current day.</td>
					</tr>
					<tr valign="top"><th scope="row">Cache life time</th>
					
						<td><input type="TEXT" name="ti_stat[life_time]" value="<?php echo $options['life_time']; ?>" /></td>
						<td>Time (in seconds) between cache rebuilds. If value is more then 24h. WP-Cron run ignores this. Set to 86400 or more to disable cache rebuild between cronjobs.</td>
					</tr>
					<tr valign="top"><th scope="row">Schedule hour</th>
					
						<td><select name="ti_stat[schedule]">
								<option <?php if ('00' == $options['schedule']) {echo 'selected';} ?> value="00">00:00</option>
								<option <?php if ('01' == $options['schedule']) {echo 'selected';} ?> value="01">01:00</option>
								<option <?php if ('02' == $options['schedule']) {echo 'selected';} ?> value="02">02:00</option>
								<option <?php if ('03' == $options['schedule']) {echo 'selected';} ?> value="03">03:00</option>
								<option <?php if ('04' == $options['schedule']) {echo 'selected';} ?> value="04">04:00</option>
								<option <?php if ('05' == $options['schedule']) {echo 'selected';} ?> value="05">05:00</option>
								<option <?php if ('06' == $options['schedule']) {echo 'selected';} ?> value="06">06:00</option>
								<option <?php if ('07' == $options['schedule']) {echo 'selected';} ?> value="07">07:00</option>
								<option <?php if ('08' == $options['schedule']) {echo 'selected';} ?> value="08">08:00</option>
								<option <?php if ('09' == $options['schedule']) {echo 'selected';} ?> value="09">09:00</option>
								<option <?php if ('10' == $options['schedule']) {echo 'selected';} ?> value="10">10:00</option>
								<option <?php if ('11' == $options['schedule']) {echo 'selected';} ?> value="11">11:00</option>
								<option <?php if ('12' == $options['schedule']) {echo 'selected';} ?> value="12">12:00</option>
								<option <?php if ('13' == $options['schedule']) {echo 'selected';} ?> value="13">13:00</option>
								<option <?php if ('14' == $options['schedule']) {echo 'selected';} ?> value="14">14:00</option>
								<option <?php if ('15' == $options['schedule']) {echo 'selected';} ?> value="15">15:00</option>
								<option <?php if ('16' == $options['schedule']) {echo 'selected';} ?> value="16">16:00</option>
								<option <?php if ('17' == $options['schedule']) {echo 'selected';} ?> value="17">17:00</option>
								<option <?php if ('18' == $options['schedule']) {echo 'selected';} ?> value="18">18:00</option>
								<option <?php if ('19' == $options['schedule']) {echo 'selected';} ?> value="19">19:00</option>
								<option <?php if ('20' == $options['schedule']) {echo 'selected';} ?> value="20">20:00</option>
								<option <?php if ('21' == $options['schedule']) {echo 'selected';} ?> value="21">21:00</option>
								<option <?php if ('22' == $options['schedule']) {echo 'selected';} ?> value="22">22:00</option>
								<option <?php if ('23' == $options['schedule']) {echo 'selected';} ?> value="23">23:00</option>

	
								
							</select>	
						</td>
						<td>This is time on server. Check timezone difference. Value in 24 format (integer 0-23)</td>
					</tr>
					<tr valign="top"><th scope="row">Token</th>
					
						<td><?php if ( current_user_can('manage_options') ) { echo $auth->token;} else {_e('Only administrator can see this','ti-stat');} ?></td>
						<td>This is acess token to Metrika API. Do not show it to anyone!</td>
					</tr>
					<tr valign="top"><th scope="row">Metrika ID</th>
					
						<td><?php echo $auth->counter_id;?></td>
						<td>This is ID of your site in Yandex.Metrika</td>
					</tr>
					
					<tr valign="top"><th scope="row">Rebuild cache now?</th>

						<td><input type="checkbox" value="true" name="ti_stat[rebuild]" /></td>
						<td>Check this to force rebuilding cache right now.</td>

					</tr>
					
					<tr valign="top"><th scope="row">Clear all data?</th>

						<td><input type="checkbox" value="true" name="ti_stat[delete_token]" /></td>
						<td>All data, including authorisation, will be wiped.</td>

					</tr>		
				</table>
				
			
			<?php
			
			
			if ($auth->counter_id!= '')
					{
					
				echo 'debug'.$auth->counter_id;	
					
				} else {
					echo 'debug_false';
					$counters = ti_counters($auth->token);
									
				?>
				<h2>Chose your site ID</h2>
				
				<?php if ($counters) {?>
				<table class="form-table">
					<?php foreach ($counters as $counter) 
						{?>
					<tr valign="top">
						<td><?php echo $counter['name']?></td>
						<td><input type="radio" name="ti_stat[counter]" value="<?php echo $counter['id']; ?>" <?php if ($couner['id']==$auth->counter_id){echo 'CHECKED';}?>/></td>
						<td>URL:<?php echo $counter['url']; ?></td>
						<td>Permission:<?php echo $counter['permission']; ?></td>
					</tr>
					
						<?php } ?>

				</table>
				<?php } else {echo "You don't have counters in Yandex.Metrika";}?>
				

				
				<?php
				} ?>
				
				
			
			<?php
			
			} else { 
			?>
			
			
			
				<h2>User Auth</h2>
			
				<table class="form-table">
			
					<tr valign="top"><th scope="row">Username</th>

						<td><input type="text" name="ti_stat[username]" value="<?php echo $options['username']; ?>" />@yandex.ru</td>

					</tr>
					<tr valign="top"><th scope="row">Password</th>

						<td><input type="password" name="ti_stat[password]" value="<?php echo $options['password']; ?>" /></td>

					</tr>					
				</table>
			
				<?php
				
			} ?>
			
			
			
			<p class="submit">
            <input type="submit" class="button-primary" value="Save Changes" />
            </p>
	<?php

	
	
	}
	
	
	if ('graphs' == $tab) {
	
	$token = $auth->token;
					$id = $auth->counter_id;
					
					
					require_once('Yreport.php');
					$widget = get_option('ti_stat_widget');
					$table = new tableReport('popular','popular');
					$table->settings($widget);
					$table->data = $widget['data'];
					$table->auth($auth->token,$auth->counter_id);
					
					if ($table->ready(true)) {
					
					$popular = $table->show();
					$widget['data']=$popular;
					update_option('ti_stat_widget',$widget);
					
					?>
					<table>
						<tr>
							<td>URL</td>
							<td>Visits</td>
							<td>OUT</td>
							<td>IN</td>
						</tr>
						
						<?php for ($i=0;$i<$table->count;$i++){?>
						<tr>
							<?php foreach ($popular[$i] as $td) {?>
							<td><?php echo $td;?></td>
							<?php } ?>

						</tr>
						<?php } ?>

					
					</table>
					<?php
					} else {
						echo 'Error='.$table->error;
						}
					
					?>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/amcharts.js';?>"></script>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/amfallback.js';?>"></script>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/raphael.js';?>"></script>
					
					
					
					<?php
					$countries = new pieReport('countries','countries');
					$countries->auth($auth->token,$auth->counter_id);
					$countries->settings($options);
					
					$age = new pieReport('age','age');
					$age->auth($auth->token,$auth->counter_id);
					$age->settings($options);
					
					$gender = new pieReport('gender','gender');
					$gender->auth($auth->token,$auth->counter_id);
					$gender->settings($options);
					
					$source = new pieReport('source','source');
					$source->auth($auth->token,$auth->counter_id);
					$source->settings($options);
					
					$traffic = new lineReport('traffic','traffic');
					$traffic->auth($auth->token,$auth->counter_id);
					$traffic->settings($options);
					
					$hourly = new lineReport('hourly','hourly');
					$hourly->auth($auth->token,$auth->counter_id);
					$hourly->settings($options);
					
					?>
					<div id="traffic" style="width:600px; height:500px; background-color:#FFFFFF"></div>					
					<div id="hourly" style="width:600px; height:500px; background-color:#FFFFFF"></div>					
					<div id="age" style="width:600px; height:400px; background-color:#FFFFFF"></div>					
					<div id="gender" style="width:600px; height:400px; background-color:#FFFFFF"></div>					
					<div id="source" style="width:600px; height:500px; background-color:#FFFFFF"></div>					
					
					<div id="countries" style="width:600px; height:600px; background-color:#FFFFFF"></div>					
					<?php
					$hourly->ready($input['rebuild']);
					echo $hourly->show();
					
					$traffic->ready($input['rebuild']);
					echo $traffic->show();
					
					$countries->ready($input['rebuild']);
					echo $countries->show();
					$age->ready($input['rebuild']);
					echo $age->show();
					$gender->ready($input['rebuild']);
					echo $gender->show();
					$source->ready($input['rebuild']);
					echo $source->show();
		}
	
	
	if ('hourly' == $tab) {
	
	$graph_settings = get_option('ti_stat_graph');
	

	
	
	
	if ('true' == $_GET['settings-updated']) {
	$graph_settings['hourly']['font'] = 'Tahoma'; 
	
	
	if ('' != $input['label']) {
		$graph_settings['hourly']['label'] = $input['label'];
		}
	
	if ('' != $input['l0']['title']) {
		$graph_settings['hourly']['l0']['title'] = $input['l0']['title'];
		}
	if ('' != $input['l0']['color']) {
		$graph_settings['hourly']['l0']['color'] = $input['l0']['color'];
		}
	if ('' != $input['l0']['fill_alpha']) {
		$graph_settings['hourly']['l0']['fill_alpha'] = $input['l0']['fill_alpha'];
		}
	if ('' != $input['l0']['line_width']) {
		$graph_settings['hourly']['l0']['line_width'] = $input['l0']['line_width'];
		}
	if ('' != $input['l0']['color_hover']) {
		$graph_settings['hourly']['l0']['color_hover'] = $input['l0']['color_hover'];
		}	
	
	if ('' != $input['l1']['title']) {
		$graph_settings['hourly']['l1']['title'] = $input['l1']['title'];
		}
	if ('' != $input['l1']['color']) {
		$graph_settings['hourly']['l1']['color'] = $input['l1']['color'];
		}
	if ('' != $input['l1']['fill_alpha']) {
		$graph_settings['hourly']['l1']['fill_alpha'] = $input['l1']['fill_alpha'];
		}
	if ('' != $input['l1']['line_width']) {
		$graph_settings['hourly']['l1']['line_width'] = $input['l1']['line_width'];
		}
	if ('' != $input['l1']['color_hover']) {
		$graph_settings['hourly']['l1']['color_hover'] = $input['l1']['color_hover'];
		}			
	
	if ('' != $input['l2']['title']) {
		$graph_settings['hourly']['l2']['title'] = $input['l2']['title'];
		}
	if ('' != $input['l2']['color']) {
		$graph_settings['hourly']['l2']['color'] = $input['l2']['color'];
		}
	if ('' != $input['l2']['fill_alpha']) {
		$graph_settings['hourly']['l2']['fill_alpha'] = $input['l2']['fill_alpha'];
		}
	if ('' != $input['l2']['line_width']) {
		$graph_settings['hourly']['l2']['line_width'] = $input['l2']['line_width'];
		}
	if ('' != $input['l2']['color_hover']) {
		$graph_settings['hourly']['l2']['color_hover'] = $input['l2']['color_hover'];
		}	

		$graph_settings['hourly']['l0']['selected'] = '1';
		$graph_settings['hourly']['l1']['selected'] = '1';
		$graph_settings['hourly']['l2']['selected'] = '1';

	
	
	
	
	
	update_option('ti_stat_graph', $graph_settings);
	
	ti_stat_settings_xml('hourly');
	}
	?>
			
			<table class="form-table">
					<tr valign="top"><th scope="row">Название</th>
					
						<td><input type="TEXT" name="ti_stat[label]" value="<?php echo $graph_settings['hourly']['label']; ?>" /></td>
						<td></td>	
					</tr>
					<tr>
						<td>
						<b>График посетителей</b>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Подпись</th>
					
						<td><input type="TEXT" name="ti_stat[l0][title]" value="<?php echo $graph_settings['hourly']['l0']['title']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет графика</th>
					
						<td><input type="TEXT" name="ti_stat[l0][color]" value="<?php echo $graph_settings['hourly']['l0']['color']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Степень прозрачности заполнения</th>
					
						<td><input type="TEXT" name="ti_stat[l0][fill_alpha]" value="<?php echo $graph_settings['hourly']['l0']['fill_alpha']; ?>" /></td>
						<td>Целое число, где 0 - без заполнения, а 100 - абсолютно непрозрачная заливка.</td>	
					</tr>
					<tr valign="top"><th scope="row">Толщина линии</th>
					
						<td><input type="TEXT" name="ti_stat[l0][line_width]" value="<?php echo $graph_settings['hourly']['l0']['line_width']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет при наведении</th>
					
						<td><input type="TEXT" name="ti_stat[l0][color_hover]" value="<?php echo $graph_settings['hourly']['l0']['color_hover']; ?>" /></td>
						<td></td>	
					</tr>
					<tr>
						<td>
						<b>Время проведенное на сайте</b>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Подпись</th>
					
						<td><input type="TEXT" name="ti_stat[l1][title]" value="<?php echo $graph_settings['hourly']['l1']['title']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет графика</th>
					
						<td><input type="TEXT" name="ti_stat[l1][color]" value="<?php echo $graph_settings['hourly']['l1']['color']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Степень прозрачности заполнения</th>
					
						<td><input type="TEXT" name="ti_stat[l1][fill_alpha]" value="<?php echo $graph_settings['hourly']['l1']['fill_alpha']; ?>" /></td>
						<td>Целое число, где 0 - без заполнения, а 100 - абсолютно непрозрачная заливка.</td>	
					</tr>
					<tr valign="top"><th scope="row">Толщина линии</th>
					
						<td><input type="TEXT" name="ti_stat[l1][line_width]" value="<?php echo $graph_settings['hourly']['l1']['line_width']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет при наведении</th>
					
						<td><input type="TEXT" name="ti_stat[l1][color_hover]" value="<?php echo $graph_settings['hourly']['l1']['color_hover']; ?>" /></td>
						<td></td>	
					</tr>
					<tr>
						<td>
						<b>Глубина просмотра</b>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Подпись</th>
					
						<td><input type="TEXT" name="ti_stat[l2][title]" value="<?php echo $graph_settings['hourly']['l2']['title']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет графика</th>
					
						<td><input type="TEXT" name="ti_stat[l2][color]" value="<?php echo $graph_settings['hourly']['l2']['color']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Степень прозрачности заполнения</th>
					
						<td><input type="TEXT" name="ti_stat[l2][fill_alpha]" value="<?php echo $graph_settings['hourly']['l2']['fill_alpha']; ?>" /></td>
						<td>Целое число, где 0 - без заполнения, а 100 - абсолютно непрозрачная заливка.</td>	
					</tr>
					<tr valign="top"><th scope="row">Толщина линии</th>
					
						<td><input type="TEXT" name="ti_stat[l2][line_width]" value="<?php echo $graph_settings['hourly']['l2']['line_width']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет при наведении</th>
					
						<td><input type="TEXT" name="ti_stat[l2][color_hover]" value="<?php echo $graph_settings['hourly']['l2']['color_hover']; ?>" /></td>
						<td></td>	
					</tr>
			

			</table>
					
			
			<p class="submit">
            <input type="submit" class="button-primary" value="Save Changes" />
            </p>
			
	
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/amcharts.js';?>"></script>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/amfallback.js';?>"></script>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/raphael.js';?>"></script>
					<div id="hourly" style="width:600px; height:500px; background-color:#FFFFFF"></div>
					
					
					<?php		
	
		require_once('Yreport.php');
		$hourly = new lineReport('hourly','hourly');
		$hourly->auth($auth->token,$auth->counter_id);
		$hourly->settings($options);
		 
		echo $hourly->show();
	
	}
	
	
	
	
	if ('traffic' == $tab) {
	
	
	
	$graph_settings = get_option('ti_stat_graph');
	if ('true' == $_GET['settings-updated']) {
	$graph_settings['traffic']['font'] = 'Tahoma'; 
	
	if ('' != $input['label']) {
		$graph_settings['traffic']['label'] = $input['label'];
		}
	
	if ('' != $input['l0']['title']) {
		$graph_settings['traffic']['l0']['title'] = $input['l0']['title'];
		}
	if ('' != $input['l0']['color']) {
		$graph_settings['traffic']['l0']['color'] = $input['l0']['color'];
		}
	if ('' != $input['l0']['fill_alpha']) {
		$graph_settings['traffic']['l0']['fill_alpha'] = $input['l0']['fill_alpha'];
		}
	if ('' != $input['l0']['line_width']) {
		$graph_settings['traffic']['l0']['line_width'] = $input['l0']['line_width'];
		}
	if ('' != $input['l0']['color_hover']) {
		$graph_settings['traffic']['l0']['color_hover'] = $input['l0']['color_hover'];
		}	
	
	if ('' != $input['l1']['title']) {
		$graph_settings['traffic']['l1']['title'] = $input['l1']['title'];
		}
	if ('' != $input['l1']['color']) {
		$graph_settings['traffic']['l1']['color'] = $input['l1']['color'];
		}
	if ('' != $input['l1']['fill_alpha']) {
		$graph_settings['traffic']['l1']['fill_alpha'] = $input['l1']['fill_alpha'];
		}
	if ('' != $input['l1']['line_width']) {
		$graph_settings['traffic']['l1']['line_width'] = $input['l1']['line_width'];
		}
	if ('' != $input['l1']['color_hover']) {
		$graph_settings['traffic']['l1']['color_hover'] = $input['l1']['color_hover'];
		}			
	
	if ('' != $input['l2']['title']) {
		$graph_settings['traffic']['l2']['title'] = $input['l2']['title'];
		}
	if ('' != $input['l2']['color']) {
		$graph_settings['traffic']['l2']['color'] = $input['l2']['color'];
		}
	if ('' != $input['l2']['fill_alpha']) {
		$graph_settings['traffic']['l2']['fill_alpha'] = $input['l2']['fill_alpha'];
		}
	if ('' != $input['l2']['line_width']) {
		$graph_settings['traffic']['l2']['line_width'] = $input['l2']['line_width'];
		}
	if ('' != $input['l2']['color_hover']) {
		$graph_settings['traffic']['l2']['color_hover'] = $input['l2']['color_hover'];
		}	

		$graph_settings['traffic']['l0']['selected'] = '1';
		$graph_settings['traffic']['l1']['selected'] = '1';
		$graph_settings['traffic']['l2']['selected'] = '1';

	
	
	
	
	
	update_option('ti_stat_graph', $graph_settings);
	
	ti_stat_settings_xml('traffic');
	}
	?>
			
			<table class="form-table">
					<tr valign="top"><th scope="row">Название</th>
					
						<td><input type="TEXT" name="ti_stat[label]" value="<?php echo $graph_settings['traffic']['label']; ?>" /></td>
						<td></td>	
					</tr>
					<tr>
						<td>
						<b>График посещений</b>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Подпись</th>
					
						<td><input type="TEXT" name="ti_stat[l0][title]" value="<?php echo $graph_settings['traffic']['l0']['title']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет графика</th>
					
						<td><input type="TEXT" name="ti_stat[l0][color]" value="<?php echo $graph_settings['traffic']['l0']['color']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Степень прозрачности заполнения</th>
					
						<td><input type="TEXT" name="ti_stat[l0][fill_alpha]" value="<?php echo $graph_settings['traffic']['l0']['fill_alpha']; ?>" /></td>
						<td>Целое число, где 0 - без заполнения, а 100 - абсолютно непрозрачная заливка.</td>	
					</tr>
					<tr valign="top"><th scope="row">Толщина линии</th>
					
						<td><input type="TEXT" name="ti_stat[l0][line_width]" value="<?php echo $graph_settings['traffic']['l0']['line_width']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет при наведении</th>
					
						<td><input type="TEXT" name="ti_stat[l0][color_hover]" value="<?php echo $graph_settings['traffic']['l0']['color_hover']; ?>" /></td>
						<td></td>	
					</tr>
					<tr>
						<td>
						<b>График посетителей</b>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Подпись</th>
					
						<td><input type="TEXT" name="ti_stat[l1][title]" value="<?php echo $graph_settings['traffic']['l1']['title']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет графика</th>
					
						<td><input type="TEXT" name="ti_stat[l1][color]" value="<?php echo $graph_settings['traffic']['l1']['color']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Степень прозрачности заполнения</th>
					
						<td><input type="TEXT" name="ti_stat[l1][fill_alpha]" value="<?php echo $graph_settings['traffic']['l1']['fill_alpha']; ?>" /></td>
						<td>Целое число, где 0 - без заполнения, а 100 - абсолютно непрозрачная заливка.</td>	
					</tr>
					<tr valign="top"><th scope="row">Толщина линии</th>
					
						<td><input type="TEXT" name="ti_stat[l1][line_width]" value="<?php echo $graph_settings['traffic']['l1']['line_width']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет при наведении</th>
					
						<td><input type="TEXT" name="ti_stat[l1][color_hover]" value="<?php echo $graph_settings['traffic']['l1']['color_hover']; ?>" /></td>
						<td></td>	
					</tr>
					<tr>
						<td>
						<b>График просмотра страниц</b>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Подпись</th>
					
						<td><input type="TEXT" name="ti_stat[l2][title]" value="<?php echo $graph_settings['traffic']['l2']['title']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет графика</th>
					
						<td><input type="TEXT" name="ti_stat[l2][color]" value="<?php echo $graph_settings['traffic']['l2']['color']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Степень прозрачности заполнения</th>
					
						<td><input type="TEXT" name="ti_stat[l2][fill_alpha]" value="<?php echo $graph_settings['traffic']['l2']['fill_alpha']; ?>" /></td>
						<td>Целое число, где 0 - без заполнения, а 100 - абсолютно непрозрачная заливка.</td>	
					</tr>
					<tr valign="top"><th scope="row">Толщина линии</th>
					
						<td><input type="TEXT" name="ti_stat[l2][line_width]" value="<?php echo $graph_settings['traffic']['l2']['line_width']; ?>" /></td>
						<td></td>	
					</tr>
					<tr valign="top"><th scope="row">Цвет при наведении</th>
					
						<td><input type="TEXT" name="ti_stat[l2][color_hover]" value="<?php echo $graph_settings['traffic']['l2']['color_hover']; ?>" /></td>
						<td></td>	
					</tr>
			

			</table>
					
			
			<p class="submit">
            <input type="submit" class="button-primary" value="Save Changes" />
            </p>
			
	
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/amcharts.js';?>"></script>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/amfallback.js';?>"></script>
					<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/ti-stat/raphael.js';?>"></script>
					<div id="traffic" style="width:600px; height:500px; background-color:#FFFFFF"></div>
					
					
					<?php		
	
		require_once('Yreport.php');
		$traffic = new lineReport('traffic','traffic');
		$traffic->auth($auth->token,$auth->counter_id);
		$traffic->settings($options);
		 
		echo $traffic->show();
	
	}
	
?>        </form>

    </div>  <?php	
	
	
}

function widget_ti_stat($args){
	require_once('Yreport.php');
	require_once('Yauth.php');
	$table = new tableReport('popular','popular');
	$options = get_option('ti_stat_widget');
	$table->settings($options);

	$auth = get_option('ti_yauth');
	extract($args);
	
	$title = $options['title'];
	echo $before_widget;

    if (!empty($title)) 
	{ 
        echo ($before_title. $title . $after_title);  

    }
	
	
	
	
	
	if ((time() > ($options['create_time'] + $options['life_time']))OR (''== $options['data'])) {
	

	
	if (($auth->check_token())and($auth->counter_id!='')) {
		$table->auth($auth->token,$auth->counter_id);
		
		$table->ready(true);
		
		$options['create_time']=time();
		$options['data'] = '';
		$popular = $table->show();
		?>
		<ul>
		<?php for ($i=0;$i<$table->count;$i++){?>
			<li>
				
					<?php
					$url = $popular[$i]['url'];
					$slug_to_get = substr($url,strlen(get_bloginfo('url'))+1,-1);
					if ($slug_to_get != '') {
						$ars=array(
							'name' => $slug_to_get,
							// 'post_type' => 'post',
							'post_status' => 'publish',
							'showposts' => 1,
							'caller_get_posts'=> 1
						);
						$my_posts = get_posts($ars);
						if( $my_posts ) {
							$options['data'] .= '<a href="'.$url.'">'.$my_posts[0]->post_title.'</a><br />';
						} else {
							$my_page = get_page_by_path($slug_to_get);
							if($my_page){
								$options['data'] .= '<a href="'.$url.'">'.$my_page->post_title.'</a><br />';
							} else {
								$options['data'] .= '<a href="'.$url.'">'.$slug_to_get.'</a><br />';
							}
						}
						
					} else {
						$options['data'] .= '<a href="'.$url.'">HOME</a><br />';
					}
					?>
				
			</li>
		<?php } ?>
		</ul>
		<?php
		update_option('ti_stat_widget',$options);
		echo $options['data'];
		} else {
		
		if (is_user_logged_in())
		{
		_e('Administrator should check <a href="/wp-admin/options-general.php?page=ti_stat">settings page</a>','ti-stat');
		echo '<br />'.$auth->error;
		} else {
		echo $options['data'];
		}
		}
		
		
		
	} else {
	
	
	echo $options['data'];
	}

	
	

	
	
		if ('true' != $options['promote'])
		{
		echo '<p style="text-align: right;">by <a  href="http://tigor.org.ua/ti-stat/">TI Stat</a></p>';
		}
	echo $after_widget;
	}
	
	
function control_ti_stat() {	
	require_once('Yreport.php');
	
	$options = $newoptions = get_option('ti_stat_widget');
	$popular = new tableReport('popular','popular');
	$popular->settings($options);
	if ( $_POST["ti-stat-widget-submit"] ) 	{
		$newoptions['title'] = $_POST["ti_stat_widget_title"];
		$newoptions['days'] = $_POST["ti_stat_days"];
		$newoptions['today'] = $_POST["ti_stat_today"];
		$newoptions['life_time'] = $_POST["ti_stat_life_time"];
		$newoptions['count']=$_POST['ti_stat_count'];
		$newoptions['exp_time']= 0;
		$newoptions['promote'] = $_POST['promote'];
		$newoptions['data'] = $options['data'];
	}
	
	$title = $options['title'];
	
	
	if ($options != $newoptions)	{
		$options = $newoptions;
		$popular->settings($options);
		update_option('ti_stat_widget', $options);
		}
	
	
	
	
	?> <dl>
		<dt>Widget title</dt>
		<dd><input name="ti_stat_widget_title" type="text" value="<?php print ($title); ?>" /></dd>
	</dl>	<dl>
		<dt>Links to show</dt>
		<dd><input name="ti_stat_count" type="text" value="<?php print ($popular->count); ?>" /></dd>
	</dl>	<dl>
		<dt>Days to include in report</dt>
		<dd><input name="ti_stat_days" type="text" value="<?php print ($popular->days); ?>" /></dd>
	</dl>	<dl>
		<dt>Use today</dt>
		<dd><input name="ti_stat_today" type="checkbox" value="<?php if ($popular->today){echo 'CHECKED';}; ?>" /></dd>
	</dl>	<dl>
		<dt>Cache time to live</dt>
		<dd><input name="ti_stat_life_time" type="text" value="<?php print ($popular->life_time); ?>" /></dd>
	</dl>	<dl>
		<dt>Hide TI Stat Link?</dt>
		<dd><input name="promote" type="checkbox" value="true" <?php if ('true' == $options['promote']) {echo 'CHECKED';} ?>/></dd>
	</dl><input type="hidden" id="ti-stat-widget-submit" name="ti-stat-widget-submit" value="1" / ><?php
	
	}




function ti_stat_init() 	{
	// Executes on plugin loading
	add_shortcode( 'ystat', 'ti_stat_shortcode' );
	require_once('Yauth.php');
	register_sidebar_widget(__('TI Stat Widget'), 'widget_ti_stat');
	register_widget_control(__('TI Stat Widget'), 'control_ti_stat');
	}
	
function admin_ti_stat_menu() {
add_options_page('TI Stat', 'TI Stat', 'manage_options', 'ti_stat', 'admin_ti_stat_options');
add_action( 'admin_init', 'register_ti_stat_settings' );

}


function ti_stat_shortcode($atts) {	
	$chart = $atts ['charts'];
	$width = $atts ['width'];
	$height= $atts ['height'];
	
	if ('' == $width)
		{$width = '600px';}
		
	if ('' == $height)
		{$height = '600px';}
	
	require_once('Yreport.php');
	$options = get_option('ti_stat_options');
	$auth = get_option('ti_yauth');
	$am = '	
			<script type="text/javascript" src="'. WP_PLUGIN_URL.'/ti-stat/amcharts.js"></script>
			<script type="text/javascript" src="'. WP_PLUGIN_URL.'/ti-stat/amfallback.js"></script>
			<script type="text/javascript" src="'. WP_PLUGIN_URL.'/ti-stat/raphael.js"></script>';
	if (($auth->counter_id != '')and($auth->check_token()))
		{	
		$token = $auth->token;
		$id = $auth->counter_id;
		
		
		if ($chart == ''){

					$countries = new pieReport('countries','countries');
					$countries->auth($auth->token,$auth->counter_id);
					$countries->settings($options);
					
					$age = new pieReport('age','age');
					$age->auth($auth->token,$auth->counter_id);
					$age->settings($options);
					
					$gender = new pieReport('gender','gender');
					$gender->auth($auth->token,$auth->counter_id);
					$gender->settings($options);
					
					$source = new pieReport('source','source');
					$source->auth($auth->token,$auth->counter_id);
					$source->settings($options);
					
					$traffic = new lineReport('traffic','traffic');
					$traffic->auth($auth->token,$auth->counter_id);
					$traffic->settings($options);
					
					$hourly = new lineReport('hourly','hourly');
					$hourly->auth($auth->token,$auth->counter_id);
					$hourly->settings($options);
					
					if ($traffic->ready())
						{
						$am .= '<div id="traffic" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
						$am .= $traffic->show();
						}
						
					if ($hourly->ready())
						{
						$am .= '<div id="hourly" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
						$am .= $hourly->show();
						}
						
					if ($countries->ready())
						{
						$am .= '<div id="countries" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
						$am .= $countries->show();
						}
					
					if ($age->ready())
						{
						$am .= '<div id="age" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
						$am .= $age->show();
						}
					
					if ($gender->ready())
						{
						$am .= '<div id="gender" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
						$am .= $gender->show();
						}
					
					if ($source->ready())
						{
						$am .= '<div id="source" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
						$am .= $source->show();
						}
				
				
				
				
			} else {
			
			$cha = explode(',',$chart);
				foreach ($cha as $c)
					{
					switch ($c){
						case 'traffic':
							$traffic = new lineReport('traffic','traffic');
							$traffic->auth($auth->token,$auth->counter_id);
							$traffic->settings($options);
							if ($traffic->ready())
								{
								$am .= '<div id="traffic" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
								$am .= $traffic->show();
								}
							break;
						case 'hourly':
							$hourly = new lineReport('hourly','hourly');
							$hourly->auth($auth->token,$auth->counter_id);
							$hourly->settings($options);
							if ($hourly->ready())
								{
								$am .= '<div id="hourly" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
								$am .= $hourly->show();
								}
							break;
						case 'countries':
							$countries = new pieReport('countries','countries');
							$countries->auth($auth->token,$auth->counter_id);
							$countries->settings($options);
							if ($countries->ready())
								{
								$am .= '<div id="countries" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
								$am .= $countries->show();
								}
							break;
						case 'age':
							$age = new pieReport('age','age');
							$age->auth($auth->token,$auth->counter_id);
							$age->settings($options);
							if ($age->ready())
								{
								$am .= '<div id="age" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
								$am .= $age->show();
								}
							break;
						case 'gender':
							$gender = new pieReport('gender','gender');
							$gender->auth($auth->token,$auth->counter_id);
							$gender->settings($options);
							if ($gender->ready())
								{
								$am .= '<div id="gender" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
								$am .= $gender->show();
								}
							break;
						case 'source':
							$source = new pieReport('source','source');
							$source->auth($auth->token,$auth->counter_id);
							$source->settings($options);
							if ($source->ready())
								{
								$am .= '<div id="source" style="width:'.$width.'; height:'.$height.'; background-color:#FFFFFF"></div>';
								$am .= $source->show();
								}
							break;
						default:
							$am .= '<div>No such report:'.$c.'</div>';
						}
					}
			
			
			
			
			}
			
		} else {
			$am = 'Not configured yet';
		}
				
				

	return $am;
}

function ti_stat_time() {
	$options = get_option('ti_stat_options');
	$format = 'd-M-Y';
	$date = date($format);
	$date = $date.' '.$options['schedule'].':00:00';
	$time = strtotime($date);
	
	return $time;
	}

function ti_stat_daily ()	{
		require_once('Yauth.php');
		require_once('Yreport.php');
		$auth = get_option('ti_yauth');
		$options = get_option('ti_stat_options');
		if (($auth->counter_id != '')and($auth->check_token()))
			{
					$countries = new pieReport('countries','countries');
					$countries->auth($auth->token,$auth->counter_id);
					$countries->settings($options);
					$countries->ready(true);
					
					$age = new pieReport('age','age');
					$age->auth($auth->token,$auth->counter_id);
					$age->settings($options);
					$age->ready(true);
					
					$gender = new pieReport('gender','gender');
					$gender->auth($auth->token,$auth->counter_id);
					$gender->settings($options);
					$gender->ready(true);
					
					$source = new pieReport('source','source');
					$source->auth($auth->token,$auth->counter_id);
					$source->settings($options);
					$source->ready(true);
					
					$traffic = new lineReport('traffic','traffic');
					$traffic->auth($auth->token,$auth->counter_id);
					$traffic->settings($options);
					$traffic->ready(true);
					
					$hourly = new lineReport('hourly','hourly');
					$hourly->auth($auth->token,$auth->counter_id);
					$hourly->settings($options);
					$hourly->ready(true);
			}
	
	}

	
function ti_stat_settings_xml($graph_name)	{

	$options = get_option('ti_stat_options');
	$options['settings_time'] = time();
	update_option('ti_stat_options', $options );
	$settings = get_option('ti_stat_graph');
	$graph_settings = $settings[$graph_name];
	$xml = '';
	if (('hourly'==$graph_name)OR('traffic'==$graph_name)){
		
		
	$xml = '<settings>
  <font>'.$graph_settings['font'].'</font>
  <hide_bullets_count>18</hide_bullets_count>
    <preloader_on_reload>1</preloader_on_reload>
  <background>
    <alpha>90</alpha>
    <border_alpha>10</border_alpha>
  </background>
  <plot_area>
    <margins>
      <left>50</left>
      <right>40</right>
      <bottom>65</bottom>
    </margins>
  </plot_area>
  <grid>
    <x>
      <alpha>10</alpha>
      <approx_count>9</approx_count>
    </x>
    <y_left>
      <alpha>10</alpha>
    </y_left>
  </grid>
  <axes>
    <x>
      <width>1</width>
      <color>0D8ECF</color>
    </x>
    <y_left>
      <width>1</width>
      <color>0D8ECF</color>
    </y_left>
	
  </axes>
  <values>
    <y_right>
      <min>1</min>
      <strict_min_max>1</strict_min_max>
    </y_right>
  </values>
  <indicator>
    <color>0D8ECF</color>
    <x_balloon_text_color>FFFFFF</x_balloon_text_color>
    <line_alpha>50</line_alpha>
    <selection_color>0D8ECF</selection_color>
    <selection_alpha>20</selection_alpha>
  </indicator>
  <zoom_out_button>
    <text_color_hover>FF0F00</text_color_hover>
  </zoom_out_button>
  <help>
    <button>
      <color>FCD202</color>
      <text_color>000000</text_color>
      <text_color_hover>FF0F00</text_color_hover>
    </button>
    <balloon>
      <text><![CDATA[Click on the graph to turn on/off value baloon <br/><br/>Click on legend key to show/hide graph<br/><br/>Mark the area you wish to enlarge]]></text>
      <color>FCD202</color>
      <text_color>000000</text_color>
    </balloon>
  </help>
  <graphs>
    <graph gid="l0">
      <title>'.$graph_settings['l0']['title'].'</title>
      <color>'.$graph_settings['l0']['color'].'</color>
      <color_hover>'.$graph_settings['l0']['color_hover'].'</color_hover>
      <selected>'.$graph_settings['l0']['selected'].'</selected>
	  <line_width>'.$graph_settings['l0']['line_width'].'</line_width>
	  <fill_alpha>'.$graph_settings['l0']['fill_alpha'].'</fill_alpha>
    </graph>
    <graph gid="l1">
      <title>'.$graph_settings['l1']['title'].'</title>
      <color>'.$graph_settings['l1']['color'].'</color>
      <color_hover>'.$graph_settings['l1']['color_hover'].'</color_hover>
      <selected>'.$graph_settings['l1']['selected'].'</selected>
	  <line_width>'.$graph_settings['l1']['line_width'].'</line_width>
	  <fill_alpha>'.$graph_settings['l1']['fill_alpha'].'</fill_alpha>
    </graph>
	<graph gid="l2">
      <title>'.$graph_settings['l2']['title'].'</title>
      <color>'.$graph_settings['l2']['color'].'</color>
      <color_hover>'.$graph_settings['l2']['color_hover'].'</color_hover>
      <selected>'.$graph_settings['l2']['selected'].'</selected>
	  <line_width>'.$graph_settings['l2']['line_width'].'</line_width>
	  <fill_alpha>'.$graph_settings['l2']['fill_alpha'].'</fill_alpha>
	  ';
	  
	  if ('hourly' == $graph_name) {$xml .= ' <axis>right</axis>';}
	  
	 
	  
	  
	  $xml .= '
    </graph>
  </graphs>
  <labels>
    <label lid="l0">
      <text><![CDATA[<b>'.$graph_settings['label'].'</b>]]></text>
      <y>25</y>
      <text_size>13</text_size>
      <align>center</align>
    </label>
  </labels>
</settings>';
		
		
		
		}
	
	if (('gender'==$graph_name)OR('source'==$graph_name)OR('age'==$graph_name)OR ('countries'==$graph_name)) {
		$xml = '<settings>
  <background>
    <alpha>100</alpha>
    <border_alpha>20</border_alpha>
  </background>
  <legend>
    <color>000000</color>
    <alpha>11</alpha>
    <align>center</align>
  </legend>
  <pie>
	<x>40%</x>
    <y>50%</y>
    <radius>90</radius>
    <inner_radius>30</inner_radius>
    <start_angle>5</start_angle>
  </pie>
  
  <data_labels>
    <show>{value}</show>
    <max_width>596</max_width>
    <line_alpha>48</line_alpha>
    <hide_labels_percent>1</hide_labels_percent>
  </data_labels>
</settings>';
		}
	
	
		
	
	
	if ('' != $xml) 
		{
		$file = fopen(WP_PLUGIN_DIR.'/ti-stat/settings/'.$graph_name.'.xml','w');
		fwrite($file ,$xml);
		fclose($file);
		}
	
	}
	
	
function register_ti_stat_settings() {
	register_setting('ti_stat_group','ti_stat');
	}

function ti_stat_activator()	{
	require_once('Yauth.php');
	if (! get_option('ti_yauth'))
		{
		$auth = new Yauth('019baa68dc984418bdfdfe1cdddb1bd9');
		add_option('ti_yauth', $auth,'', 'no' );
		}
		
		
		
	if (!get_option('ti_stat_options'))
		{
		$options['days'] = 30;
		$options['today'] = false;
		$options['life_time']= 86400;
		 
		
		add_option('ti_stat_options',$options, '', 'no' );
		}
		
		
	if (!get_option('ti_stat_widget'))
		{
		$options['days'] = 30;
		$options['today'] = false;
		$options['life_time']= 86400;
		 
		
		add_option('ti_stat_widget',$options, '', 'no' );
		}

	if (!get_option('ti_stat_graph'))
		{
		$graph['hourly']['font'] = 'Tahoma'; 
		$graph['hourly']['label'] = 'Averenge statistic by hours'; 
		
		$graph['hourly']['l0']['title'] = 'Visits';
		$graph['hourly']['l0']['color'] = '0D8ECF';
		$graph['hourly']['l0']['color_hover'] = 'FF0F00';
		$graph['hourly']['l0']['selected'] = '1';
		$graph['hourly']['l0']['line_width'] = '2';
		$graph['hourly']['l0']['fill_alpha'] = '30';
		
		$graph['hourly']['l1']['title'] = 'Visits Time(seconds)';
		$graph['hourly']['l1']['color'] = 'B0DE09';
		$graph['hourly']['l1']['color_hover'] = 'FF0F00';
		$graph['hourly']['l1']['selected'] = '1';
		$graph['hourly']['l1']['line_width'] = '2';
		$graph['hourly']['l1']['fill_alpha'] = '30';
			
		$graph['hourly']['l2']['title'] = 'Depth';
		$graph['hourly']['l2']['color'] = 'ff0000';
		$graph['hourly']['l2']['color_hover'] = 'FF0F00';
		$graph['hourly']['l2']['selected'] = '1';
		$graph['hourly']['l2']['line_width'] = '2';
		$graph['hourly']['l2']['fill_alpha'] = '30';
		

		$graph['traffic']['font'] = 'Tahoma'; 
		$graph['traffic']['label'] = 'Traffic statistic'; 
		
		$graph['traffic']['l0']['title'] = 'Visits';
		$graph['traffic']['l0']['color'] = '0D8ECF';
		$graph['traffic']['l0']['color_hover'] = 'FF0F00';
		$graph['traffic']['l0']['selected'] = '1';
		$graph['traffic']['l0']['line_width'] = '2';
		$graph['traffic']['l0']['fill_alpha'] = '30';

		
		$graph['traffic']['l1']['title'] = 'Visits Time(seconds)';
		$graph['traffic']['l1']['color'] = 'B0DE09';
		$graph['traffic']['l1']['color_hover'] = 'FF0F00';
		$graph['traffic']['l1']['selected'] = '1';
		$graph['traffic']['l1']['line_width'] = '2';
		$graph['traffic']['l1']['fill_alpha'] = '30';
		
		
		$graph['traffic']['l2']['title'] = 'Depth';
		$graph['traffic']['l2']['color'] = 'ff0000';
		$graph['traffic']['l2']['color_hover'] = 'FF0F00';
		$graph['traffic']['l2']['selected'] = '1';
		$graph['traffic']['l2']['line_width'] = '2';
		$graph['traffic']['l2']['fill_alpha'] = '30';		
		
		
		add_option('ti_stat_widget',$graph, '', 'no' );
		}
		
	if (!is_dir(WP_PLUGIN_DIR.'/ti-stat/settings/')){
		mkdir (WP_PLUGIN_DIR.'/ti-stat/settings/',0755);
		chmod (WP_PLUGIN_DIR.'/ti-stat/settings/',0755);
		}
	if (!is_dir(WP_PLUGIN_DIR.'/ti-stat/data/')){
		mkdir (WP_PLUGIN_DIR.'/ti-stat/data/',0755);
		chmod (WP_PLUGIN_DIR.'/ti-stat/data/',0755);
		}
	
	if (!file_exists(WP_PLUGIN_DIR.'/ti-stat/settings/hourly.xml'))
		{
		ti_stat_settings_xml('hourly');
		}
	if (!file_exists(WP_PLUGIN_DIR.'/ti-stat/settings/traffic.xml'))
		{
		ti_stat_settings_xml('traffic');
		}
	if (!file_exists(WP_PLUGIN_DIR.'/ti-stat/settings/age.xml'))
		{
		ti_stat_settings_xml('age');
		}
	if (!file_exists(WP_PLUGIN_DIR.'/ti-stat/settings/gender.xml'))
		{
		ti_stat_settings_xml('gender');
		}
	if (!file_exists(WP_PLUGIN_DIR.'/ti-stat/settings/source.xml'))
		{
		ti_stat_settings_xml('source');
		}
	if (!file_exists(WP_PLUGIN_DIR.'/ti-stat/settings/countries.xml'))
		{
		ti_stat_settings_xml('countries');
		}
	
	wp_schedule_event(ti_stat_time(), 'daily', 'ti_stat_daily_event');
	
	
	}

register_activation_hook(__FILE__,'ti_stat_activator');
add_action("plugins_loaded", "ti_stat_init");
add_action('admin_menu',"admin_ti_stat_menu");
add_action('ti_stat_daily_event', 'ti_stat_daily');

register_deactivation_hook(__FILE__, 'ti_stat_deactivation');

function ti_stat_deactivation() {
	// Execute on deactivation
	
	wp_clear_scheduled_hook('ti_stat_daily_event');
}

function ti_counters($token)
{
$xml = new SimpleXMLElement(ti_Y_API('http://api-metrika.yandex.ru/counters?oauth_token='.$token,'GET'));

$counters = $xml->counters;
$count = (int)$counters->attributes()->count;

for ($i=0;$i<$count;$i++)
	{
	
	$data[$i]['url']=(string) $counters->counter[$i]->site;
	$data[$i]['permission']=(string) $counters->counter[$i]->permission;
	$data[$i]['name']=(string) $counters->counter[$i]->name;
	$data[$i]['id']=(string) $counters->counter[$i]->id;
	}

return $data;
}


function ti_Y_API($url,$method)
{

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array($url, $method));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec($ch);


	if(curl_errno($ch))
    {
      return false;
	  echo 'Curl errno';
    }

    $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
    if ($http_code != 200)
    {
	echo $http_code;
	return false;
	}
    else
    {
	return $return;
	
    }
}




?>