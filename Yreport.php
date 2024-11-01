<?php
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

class Yreport {
// All public variables here
public $name; // Name
public $today; // Use data for current day
public $error; // Error information
public $token; // Yandex authorisation token
public $counter; // Yandex.Metrika counter id
public $data;
public $create_time;

public $exp_time;  // Time till report is expired
public $day1; // Report start day
public $day2; // Report end day
public $days; // Days in report
public $type; // Report type
public $life_time; // Cache life time



public function Yreport($name,$type){
	$this->name = $name;
	$this->today = false;
	$this->counter = 0;
	$this->data = '';
	$this->token = '';
	$this->type = $type;
	$this->exp_time = 0;
	$this->error = '';
	}
	
public function auth($token,$counter){
	$this->token = $token;
	$this->counter = $counter;
	
	return true;
	}

public function yget($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array($url, 'GET'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$xml = curl_exec($ch);
	
	
	if(curl_errno($ch))
    {
		$return = false;
		$this->error = 'curl'.curl_errno($ch);
    } else {
		$http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code != 200)
		{
			$this->error = 'http'.$http_code;
			$return = false;
		} else {
			$return = new SimpleXMLElement($xml);
			
		}
		
	}
	
	return $return;
	}

public function period() {
	$day2 = time();
	if (!$this->today)
		{
		$day2 = $day2 - 24*60*60;
		}
	$this->day2 = date('Ymd',$day2);
	$day1 = $day2 - ($this->days - 1)*24*60*60;
	$this->day1 = date('Ymd',$day1);
	
	return true;
	}

	
public function settings($settings)	{
	echo 'Settings function should be individual for each class';
	return false;
	}

public function expired() {
	$return = false;
	if (time()>($this->create_time+$this->life_time))
		{
		$return = true;
		}
	return $return;
	}

public function build() {
	$return = false;
	$this->error = 'no_build';
	return $return;
	}
	
public function ready($force = false) {
	
	if (($this->expired())or($force))
		{
		
		if ($this->build()) {
			$return = true;
			$this->exp_time = time()+$this->life_time;
			
			} else {
			$return = false;
			}
		
		
		} else {
		
		$return = true;
		}
	return $return;
	}
	
public function show() {
	echo 'Report class should have own show method';
	$this->error = 'Class show function not specified';
	return false;
	}


	

}

class tableReport extends Yreport{

public $count;


public function tableReport($name,$type){
	$this->name = $name;
	$this->today = false;
	$this->counter = 0;
	$this->token = '';
	$this->type = $type;
	$this->exp_time = 0;
	$this->error = '';
	$this->count = 10;
	$this->life_time = 24*60*60;
	$this->data = '';
	$this->days = 10;
	}

// TableReport Settings functions
public function settings($settings)	{
	$days = '';
	$today = '';
	$life_time = '';
	$count = '';
	$exp_time = '';
	$data = '';
	extract($settings);
	
	$this->settings_time = $settings_time;
	
	if ($days != ''){
		$this->days = $days;
		}
	if ($today != '') {
		$this->today = $today;
		}
	if ($life_time != '') {
		$this->life_time = $life_time;
		}
	if ($count != ''){
		$this->count = $count;
		}
	if ($exp_time != '') 	{
		$this->exp_time = $exp_time;
		}
	if ($data != ''){
		$this->data = $data;
		}
	$this->create_time = $create_time;
	$this->period();
	
	return true;
	}
	
public function popular()
	{
	$this->data = '';
	$this->period();
	
	
	$url='http://api-metrika.yandex.ru/stat/content/popular?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	
	
	if (!$xml)
	{
		$this->error = 'NotXML';
		return false;
	} else {
	
		
		if ($this->count > $xml->data[0]->attributes()->count) 
			{
				$count = $xml->data->attributes()->count;
			} else {
				$count = $this->count;
			}
		$this->count = $count;
		
		for ($i=0;$i<=$count;$i++){
			
			$ret[$i]['url']=(string) $xml->data->row[$i]->url;
			$ret[$i]['page_views']=(string) $xml->data->row[$i]->page_views;
			$ret[$i]['exit']=(string) $xml->data->row[$i]->exit;
			$ret[$i]['entrance']=(string) $xml->data->row[$i]->entrance;
			
			
		}
	$this->create_time = time();
	$this->data = $ret;
	
	return true;
	}
	
	
	}

public function build(){
	$return = false;
	
	switch ($this->type){
		case 'popular':
			$return = $this->popular();
			break;
		default:
			$this->error = 'bad_report_type';
			break;
		}
	
	
	return $return;
	}

public function show(){
	
	return $this->data;
	}



}


class pieReport extends Yreport {

public $file_data;
public $file_settings;


public function pieReport($name,$type)
	{
	$pl_url = plugins_url();
	$this->name = $name;
	$this->today = false;
	$this->counter = 0;
	$this->data = '';
	$this->token = '';
	$this->type = $type;
	$this->exp_time = 0;
	$this->error = '';
	// default settings file should be created
	$this->file_data = WP_PLUGIN_DIR.'/ti-stat/data/'.$this->name.'.xml';
	$this->file_settings = WP_PLUGIN_DIR.'/ti-stat/settings/'.$this->name.'.xml';

		
	
	
	}
	
public function settings($settings)	{
	$days = '';
	$today = '';
	$life_time = 0;
	extract($settings);
	
	$this->settings_time = $settings_time;
	
	if ($days != ''){
		$this->days = $days;
		}
	if ($today != '') {
		$this->today = $today;
		}
	if ($life_time != 0) {
		$this->life_time = $life_time;
		}
	if (file_exists($this->file_data))
		{
		$this->create_time = filemtime($this->file_data);
		} else
		{
		$this->create_time = 0;
		}
	$this->period();

	return true;
	}

public function show()	{
	$pl_url = plugins_url();
	$return ='<script type="text/javascript">
	var '.$this->name.' = new AmCharts.AmFallback();
				'.$this->name.'.settingsFile = "'.$pl_url.'/ti-stat/settings/'.$this->name.'.xml?'.$this->settings_time.'"; 
				'.$this->name.'.dataFile = "'.$pl_url.'/ti-stat/data/'.$this->name.'.xml?'.$this->create_time.'";
				'.$this->name.'.type = "pie";
				'.$this->name.'.chart_id = "'.$this->name.'";
				'.$this->name.'.write("'.$this->name.'");
				
	</script>';
	return $return;
	}

public function build() {
	$return = false;
	
	switch ($this->type){
		case 'age':
			$return = $this->age();
			break;
		case 'countries':
			$return = $this->countries();
			break;
		case 'gender':
			$return = $this->gender();
			break;
		case 'source':
			$return = $this->source();
			break;
		default:
			$this->error = 'bad_report_type';
			break;
		}
	
	
	return $return;
	}

public function age(){
	$return = true;
	$url='http://api-metrika.yandex.ru/stat/demography/age_gender/?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	$age = $xml->data[0];
	$count = $age->attributes()->count-1;
	
	$file = fopen($this->file_data,'w');
	fwrite($file , '<?xml version="1.0" encoding="UTF-8"?>');
	fwrite($file ,'<pie>');
	
	for ($i=0 ; $i<=$count; $i++)
		{
		
		$name = (string) $age->row[$i]->name;
		fwrite($file , '<slice title="'.$name.'">'.(string)$age->row[$i]->visits_percent.'</slice>');
		}
	fwrite($file,'</pie>');
	fclose($file);
	
	
	return $return;
	}

public function gender() {
		$return = true;
	$url='http://api-metrika.yandex.ru/stat/demography/age_gender/?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	$xml->data_gender;
	$file = fopen($this->file_data,'w');
	fwrite($file , '<?xml version="1.0" encoding="UTF-8"?>');
	fwrite($file ,'<pie>');

	fwrite($file , '<slice title="Мужчин">'.(string) $xml->data_gender->row[0]->visits_percent.'</slice>');
	fwrite($file , '<slice title="Женщин">'.(string) $xml->data_gender->row[1]->visits_percent.'</slice>');
	
	fwrite($file,'</pie>');
	fclose($file);
	
	
	return $return;
	}
	
public function countries() {
	$return = true;
	$url='http://api-metrika.yandex.ru/stat/geo/?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	

	$c = $xml->data;
	$other = 0;
	$count = (int) $c->attributes()->count;
	$count = $count-1;
	$file = fopen($this->file_data,'w');
	fwrite($file , '<?xml version="1.0" encoding="UTF-8"?>');
	fwrite($file ,'<pie>');
	
	
	for ($i=0;$i<$count;$i++)
		{
		$current = (int) $c->row[$i]->visits;
		
		if ($current>5)
			{
			$name = (string)$c->row[$i]->name;
			fwrite($file , '<slice title="'.$name.'">'.$current.'</slice>');
			}
		else
			{
			$other = $other+$current;
		
			}
		}
		$other=$other + $c->row[$count]->visits;
		
		if ($other>=0)
		{
		fwrite($file , '<slice title="Другие">'.$other.'</slice>');
		}
	
	
	fwrite($file,'</pie>');
	fclose($file);
	
	
	return $return;
	}
	
public function source()	{

	$return = true;
	$url='http://api-metrika.yandex.ru/stat/sources/summary?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	
	$c = $xml->data;
	$count = (int) $c->attributes()->count;
	$count = $count-1;
	
	$file = fopen($this->file_data,'w');
	fwrite($file , '<?xml version="1.0" encoding="UTF-8"?>');
	fwrite($file ,'<pie>');
	for ($i=0;$i<=$count;$i++)
		{
		fwrite($file , '<slice title="'.$c->row[$i]->name.'">'.$c->row[$i]->visits.'</slice>');
		}
	fwrite($file,'</pie>');
	fclose($file);
	
	
	return $return;
	
	
	}
	
}

class lineReport extends Yreport
{

public $file_data;
public $file_settings;


public function lineReport($name,$type)
	{
	$pl_url = plugins_url();
	$this->name = $name;
	$this->today = false;
	$this->counter = 0;
	$this->data = '';
	$this->token = '';
	$this->type = $type;
	$this->exp_time = 0;
	$this->error = '';
	// default settings file should be created
	$this->file_data = WP_PLUGIN_DIR.'/ti-stat/data/'.$this->name.'.xml';
	$this->file_settings = WP_PLUGIN_DIR.'/ti-stat/settings/'.$this->name.'.xml';
	

	
	
	}
	
public function settings($settings)	{
	$days = '';
	$today = '';
	$life_time = 0;
	extract($settings);
	$this->settings_time = $settings_time;
	
	
	if ($days != ''){
		$this->days = $days;
		}
	if ($today != '') {
		$this->today = $today;
		}
	if ($life_time != 0) {
		$this->life_time = $life_time;
		}
	
	if (file_exists($this->file_data))
		{
		$this->create_time = filemtime($this->file_data);
		} else
		{
		$this->create_time = 0;
		}
	$this->period();

	return true;
	}




public function build() {
	$return = false;
	
	switch ($this->type){
		case 'hourly':
			$return = $this->hourly();
			break;
		case 'traffic':
			$return = $this->traffic();
			break;

		default:
			$this->error = 'bad_report_type';
			break;
		}
	
	
	return $return;
	}
// This should be rewritten
public function show()	{
	$pl_url = plugins_url();
	$return ='<script type="text/javascript">
	var '.$this->name.' = new AmCharts.AmFallback();
				'.$this->name.'.settingsFile = "'.$pl_url.'/ti-stat/settings/'.$this->name.'.xml?'.$this->settings_time.'"; 
				'.$this->name.'.dataFile = "'.$pl_url.'/ti-stat/data/'.$this->name.'.xml?'.$this->create_time.'";
				'.$this->name.'.type = "line";
				'.$this->name.'.pathToImages = "'.$pl_url.'/ti-stat/";
				'.$this->name.'.chart_id = "'.$this->name.'";
				'.$this->name.'.write("'.$this->name.'");
				
	</script>';
	return $return;
	}

public function traffic()
	{
	
	$return = true;
	
	$url='http://api-metrika.yandex.ru/stat/traffic/summary/?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	
	
	$rows = $xml->data[0];
	$count = $rows->attributes()->count;
	$count = $count - 1;
	if ($count != 0) {
		$file = fopen($this->file_data,'w');
		fwrite($file , '<?xml version="1.0" encoding="UTF-8"?>'); 
		for ($i=0; $i<=$count;$i++)
			{
			$dat['name'] = (string)$rows->row[$i]->date;
			$dat['visits'] = $rows->row[$i]->visits;
			$dat['visitors'] = $rows->row[$i]->visitors;
			$dat['page_views'] = $rows->row[$i]->page_views;
			$data[$count-$i] = $dat;
			
			}
		fwrite($file, '<chart><series>');
		for ($i=0;$i<=$count;$i++)
			{
			$name = $data[$i]['name'];
			fwrite($file, '<value xid="'.$i.'">'.$name[6].$name[7].'.'.$name[4].$name[5].'</value>');
			}
		fwrite($file, '</series>');
		fwrite($file, '<graphs>');
		
		
		fwrite($file, '<graph gid="l0">');
		for ($i=0;$i<=$count;$i++)
			{
			fwrite($file, '<value xid="'.$i.'">'.$data[$i]['visits'].'</value>');
			}
		fwrite($file, '</graph>');
		
		fwrite($file, '<graph gid="l1">');
		for ($i=0;$i<=$count;$i++)
			{
			fwrite($file, '<value xid="'.$i.'">'.$data[$i]['visitors'].'</value>');
			}
		fwrite($file, '</graph>');
		
		fwrite($file, '<graph gid="l2">');
		for ($i=0;$i<=$count;$i++)
			{
			fwrite($file, '<value xid="'.$i.'">'.$data[$i]['page_views'].'</value>');
			}
		fwrite($file, '</graph>');
		
		
		fwrite($file, '</graphs>');
		fwrite($file, '</chart>');
		fclose($file);
	
	} else {
		$return = false;
		}

	return $return;

	}
public function hourly() {
	$return = true;
	
	$url='http://api-metrika.yandex.ru/stat/traffic/hourly/?id='.$this->counter.'&oauth_token='.$this->token.'&date1='.$this->day1.'&date2='.$this->day2;
	$xml = $this->yget($url);
	$hourly = $xml->data;
	$count = 23;
	for ($i=0;$i<=$count;$i++)
	{
		$data[$i]['avg_visits']= (string) $hourly->row[$i]->avg_visits;
		$data[$i]['visit_time']= (string) $hourly->row[$i]->visit_time;
		$data[$i]['depth']= (string) $hourly->row[$i]->depth;
	}
		$file = fopen($this->file_data,'w');
		fwrite($file, '<chart><series>');
		for ($i=0;$i<=$count;$i++)
			{
			
			fwrite($file, '<value xid="'.$i.'">'.$i.':00</value>');
			}
		fwrite($file, '</series>');
		fwrite($file, '<graphs>');
		
		
		fwrite($file, '<graph gid="l0">');
		for ($i=0;$i<=$count;$i++)
			{
			fwrite($file, '<value xid="'.$i.'">'.$data[$i]['avg_visits'].'</value>');
			}
		fwrite($file, '</graph>');
		
		fwrite($file, '<graph gid="l1">');
		for ($i=0;$i<=$count;$i++)
			{
			fwrite($file, '<value xid="'.$i.'">'.$data[$i]['visit_time'].'</value>');
			}
		fwrite($file, '</graph>');
		
		fwrite($file, '<graph gid="l2">');
		for ($i=0;$i<=$count;$i++)
			{
			fwrite($file, '<value xid="'.$i.'">'.$data[$i]['depth'].'</value>');
			}
		fwrite($file, '</graph>');
		
		
		fwrite($file, '</graphs>');
		fwrite($file, '</chart>');
		fclose($file);

	
	}

}
?>