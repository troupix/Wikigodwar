<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function jeu($essai)
	{
		print_r($_SESSION);
		//Recupération de la première page
		$page_content = file_get_contents( "https://fr.wikipedia.org/wiki/Sp%C3%A9cial:Page_au_hasard" );
		
		//recupération de la page cible
		$page_cible = file_get_contents( "https://fr.wikipedia.org/wiki/Sp%C3%A9cial:Page_au_hasard" );
		$titre_cible = array();
		preg_match_all("/\<h1.*\<\/h1>/u",$page_cible,$titre_cible);
		preg_match_all("/\>.*\</u",$titre_cible[0][0],$titre_cible);
		$titre_cible[0][0] = substr($titre_cible[0][0],0, -1);
		$titre_cible[0][0] = substr($titre_cible[0][0],1);
		$titre_cible[0][0]=str_replace(" ","_",$titre_cible[0][0]);
			// supression de la section edit
			$page_content= preg_replace("/\<span class=\"mw-editsection\"\>.*\]\<\/span\>\<\/span\>/","",$page_content);

						
			// suppression des portail
			$page_content= preg_replace("/\<ul id=\"bandeau-portail\" class=\"bandeau-portail\"\>.*\<script\>/s","<script>",$page_content);
			
			// supression du modifier
			$page_content= preg_replace("/\<p class=\"navbar noprint bordered\".*/","",$page_content);
			
			
			// supression du menu de navigation
			$page_content= preg_replace("/\<\!-- \nNewPP limit report.*\<script\>/s","<script>",$page_content);

			// redirection des liens
			$out = array();
			$replace = array();
			preg_match_all("/wiki\/[^\"]*\"/u",$page_content,$out);
			$replace = $out;
			for($i=0; $i<sizeof($out[0]); $i++){
				$replace[0][$i] = "/".$replace[0][$i];
				$out[0][$i] = substr($out[0][$i],0, -1);
				$out[0][$i] = "/index.php/Game/".$out[0][$i]."/0/".$titre_cible[0][0]."\"";
				$page_content= str_replace($replace[0][$i],$out[0][$i],$page_content);
			}	
		$fp = fopen("C:/EasyPHP-Devserver-17/eds-www/wikigodwar/application/views/wikiPage.php","w+");
		fwrite($fp,"");
		fputs($fp, $page_content);
		fclose ($fp);
		$toview = array();
		$toview['nombrecoup']= 0;
		$toview['cible'] = $titre_cible[0][0];
		$this->load->view('header', $toview);
		$this->load->view('wikiPage');
		
	}
	
	public function wiki($pageTitle,$nombrecoup,$pagecible){
		$nombrecoup+=1;
		if (!strstr($pageTitle,"Fichier:")){
			$page_content = file_get_contents( "https://fr.wikipedia.org/wiki/".$pageTitle);
			// supression de la section edit
			// supression de la section edit
			$page_content= preg_replace("/\<span class=\"mw-editsection\"\>.*\]\<\/span\>\<\/span\>/","",$page_content);

						
			// suppression des portail
			$page_content= preg_replace("/\<ul id=\"bandeau-portail\" class=\"bandeau-portail\"\>.*\<script\>/s","<script>",$page_content);
			
			// supression du modifier
			$page_content= preg_replace("/\<p class=\"navbar noprint bordered\".*/","",$page_content);
			
			
			// supression du menu de navigation
			$page_content= preg_replace("/\<\!-- \nNewPP limit report.*\<script\>/s","<script>",$page_content);

			// redirection des liens
			$out = array();
			$replace = array();
			preg_match_all("/wiki\/[^\"]*\"/u",$page_content,$out);
			$replace = $out;
			for($i=0; $i<sizeof($out[0]); $i++){
				$replace[0][$i] = "/".$replace[0][$i];
				$out[0][$i] = substr($out[0][$i],0, -1);
				$out[0][$i] = "/index.php/Game/".$out[0][$i]."/".$nombrecoup."/".$pagecible."\"";
				$page_content= str_replace($replace[0][$i],$out[0][$i],$page_content);
			}	

			
			$fp = fopen("C:/EasyPHP-Devserver-17/eds-www/wikigodwar/application/views/wikiPage.php","w+");
			fwrite($fp,"");
			fputs($fp, $page_content);
			fclose ($fp);
			$toview = array();
			//si victoire headervictoire
			//Recupération du titre de la page
			$titreCurrentPage = array();
			preg_match_all("/\<h1.*\<\/h1>/u",$page_content,$titreCurrentPage);
			preg_match_all("/\>.*\</u",$titreCurrentPage[0][0],$titreCurrentPage);
			$titreCurrentPage[0][0] = substr($titreCurrentPage[0][0],0, -1);
			$titreCurrentPage[0][0] = substr($titreCurrentPage[0][0],1);
			$titreCurrentPage[0][0]=str_replace(" ","_",$titreCurrentPage[0][0]);
			$toview['nombrecoup']= $nombrecoup;
			$toview['cible'] = urldecode($pagecible);
			if(strcmp($pagecible,$titreCurrentPage[0][0])==0){
				$this->load->view('headerVictoire', $toview);
			}
			else{
				$this->load->view('header', $toview);
			}
			$this->load->view('wikiPage');
		}
		else{
			$this->load->view('perduImage');
		}
	}
	

}
