<?php
/*
Plugin Name: DomuSuite Plugin
Plugin URI: http://www.domusuite.com/
Description: Plugin per la visualizzazione in wordpress degli immobili gestiti in DomuSuite
Version: 1.0
Author: Delphinet Communication Associati
Author URI: http://www.delphinet.it
*/
//require get_template_directory() . '/inc/widget.php';
class widget_search_domusuite extends WP_Widget {

	function __construct() {
		// Istanzia l'oggetto genitore
		parent::__construct( 'widget_ricerca_domusuite',__('Ricerca DomuSuite', 'ricerca_domusuite_domain'), 
 
    	// Descrizione
    	array( 'description' => __( 'Effettua la ricerca degli immobili DomuSuite', 'ricerca_domusuite_domain' ) ) 
    	);
	}

	function widget( $args, $instance ) {
		global $wp_query,$wpdb;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$ritorno = '';
		$ritorno .= '<div id="widget_ricerca_domusuite">';
		$ritorno .= '<div class="titolo">'.$title.'</div>';
		$ritorno .= '<div class="ricerca">';
		$immagine = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_content like '%%%s%%'", "[lista-immobili-domusuite"));
		$ritorno .= '<form action="/index.php" method="GET">';
		$ritorno .= '<input type="hidden" name="page_id" value="'.$immagine[0]->ID.'" />';
		$contratto = $wp_query->query_vars["search_contratto"];
		$ritorno .= '<div class="blocco_ricerca"><div class="titolo_ricerca">Contratto</div>';
		
		$ritorno .= '<div><select name="search_contratto">';
		$ritorno .= '<option value=""';
		$ritorno .= '></option>';
		$ritorno .= '<option value="Vendita"';
		if ($contratto=='Vendita') {
			$ritorno .= ' selected ';
		}
		$ritorno .= '>Vendita</option>';
		$ritorno .= '<option value="Affitto"';
		if ($contratto=='Affitto') {
			$ritorno .= ' selected ';
		}
		$ritorno .= '>Affitto</option>';
		$ritorno .= '</select></div></div>';
		
		$row_tipologia = $wpdb->get_results($wpdb->prepare("SELECT tipologia FROM ".$wpdb->prefix . "immobili_domusuite GROUP BY tipologia ORDER BY tipologia",""));
		if (count($row_tipologia)>0) {
			$contratto = $wp_query->query_vars["search_tipologia"];
			$ritorno .= '<div class="blocco_ricerca"><div class="titolo_ricerca">Tipologia</div>';
			
			$ritorno .= '<div><select name="search_tipologia">';
			$ritorno .= '<option value=""';
			$ritorno .= '></option>';
			foreach ($row_tipologia as $sng_tipologia) {
				$ritorno .= '<option value="'.$sng_tipologia->tipologia.'"';
				if ($contratto==$sng_tipologia->tipologia) {
					$ritorno .= ' selected ';
				}
				$ritorno .= '>'.ucfirst(strtolower($sng_tipologia->tipologia)).'</option>';
			}
			
			$ritorno .= '</select></div></div>';
		}
		
		
		$row_citta = $wpdb->get_results($wpdb->prepare("SELECT comune FROM ".$wpdb->prefix . "immobili_domusuite WHERE comune<>%s GROUP BY comune ORDER BY comune","''"));
		if (count($row_citta)>0) {
			$citta = $wp_query->query_vars["search_citta"];
			$ritorno .= '<div class="blocco_ricerca"><div class="titolo_ricerca">Citt&agrave;</div>';
			
			$ritorno .= '<div><select name="search_citta">';
			$ritorno .= '<option value=""';
			$ritorno .= '></option>';
			foreach ($row_citta as $sng_citta) {
				$ritorno .= '<option value="'.$sng_citta->comune.'"';
				if ($citta==$sng_citta->comune) {
					$ritorno .= ' selected ';
				}
				$ritorno .= '>'.ucfirst(strtolower($sng_citta->comune)).'</option>';
			}
			
			$ritorno .= '</select></div></div>';
		}
		
		$ritorno .= '<div class="blocco_ricerca"><div class="titolo_ricerca">Prezzo (max)</div>';
		$prezzo = $wp_query->query_vars["search_prezzo"];	
		$ritorno .= '<div><input type="input" name="search_prezzo" value="'.$prezzo.'" /></div></div>';
		
		
		
		$ritorno .= '<div class="blocco_ricerca"><div class="titolo_ricerca"></div>';
		$ritorno .= '<div><input type="submit" name="search_send" value="Ricerca" /></div></div>';
		$ritorno .= '</form>';
		$ritorno .= '</div>';
		
		$ritorno .= '</div>';
		
		echo $ritorno;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
 
        $instance['title'] = strip_tags( $new_instance['title'] );
 
        $instance['orientamento'] =  $new_instance['orientamento'];
 
        return $instance;
	}

	function form( $instance ) {
        $title = esc_attr($instance['title']);
        $orientamento = esc_attr($instance['orientamento']);  ?>
        <p><label for="<?php echo $this->get_field_id('title');?>">
        Titolo: <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title; ?>" />
        </label></p>
        <p><label for="<?php echo $this->get_field_id('orientamento');?>">
        Orientamento: <select class="widefat" id="<?php echo $this->get_field_id('orientamento');?>" name="<?php echo $this->get_field_name('orientamento');?>">
        <option value="1" <?php if ($orientamento==1) {echo ' selected ';} ?>>Orizzontale</option>
        <option value="2" <?php if ($orientamento==2) {echo ' selected ';} ?>>Verticale</option>
        </select>
        </label></p>
        <?php
    }
}

function myplugin_register_widgets() {
	register_widget( 'widget_search_domusuite' );
}

add_action( 'widgets_init', 'myplugin_register_widgets' );


function attivazione_plugin_domusuite()
{
    add_option('url_xml_domusuite', '');
    
    //CREAZIONE DATABASE
    
    global $wpdb;

    $table_name = $wpdb->prefix . 'immobili_domusuite';

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		codice varchar(255) DEFAULT NULL,
		riferimento varchar(255) DEFAULT NULL,
		categoria varchar(255) DEFAULT NULL,
		a_reddito varchar(255) DEFAULT NULL,
		reddito varchar(255) DEFAULT NULL,
		tipologia varchar(255) DEFAULT NULL,
		trattativa varchar(255) DEFAULT NULL,
		contratto varchar(255) DEFAULT NULL,
		prezzo varchar(255) DEFAULT NULL,
		canone_affitto varchar(255) DEFAULT NULL,
		comune varchar(255) DEFAULT NULL,
		provincia varchar(255) DEFAULT NULL,
		regione varchar(255) DEFAULT NULL,
		nazione varchar(255) DEFAULT NULL,
		anno_costruzione varchar(255) DEFAULT NULL,
		mq_coperti varchar(255) DEFAULT NULL,
		mq_terreno varchar(255) DEFAULT NULL,
		balconi varchar(255) DEFAULT NULL,
		mq_balconi varchar(255) DEFAULT NULL,
		terrazze varchar(255) DEFAULT NULL,
		mq_terrazze varchar(255) DEFAULT NULL,
		cantine varchar(255) DEFAULT NULL,
		mq_cantine varchar(255) DEFAULT NULL,
		box varchar(255) DEFAULT NULL,
		mq_box varchar(255) DEFAULT NULL,
		p_auto_scoperto varchar(255) DEFAULT NULL,
		p_auto_coperto varchar(255) DEFAULT NULL,
		liv_fuori_terra varchar(255) DEFAULT NULL,
		liv_interrati varchar(255) DEFAULT NULL,
		portiere varchar(255) DEFAULT NULL,
		ascensore varchar(255) DEFAULT NULL,
		descrizione_piano varchar(255) DEFAULT NULL,
		stato varchar(255) DEFAULT NULL,
		ipe varchar(255) DEFAULT NULL,
		classe_energetica varchar(255) DEFAULT NULL,
		riscaldamento varchar(255) DEFAULT NULL,
		nr_balconi varchar(255) DEFAULT NULL,
		nr_terrazze varchar(255) DEFAULT NULL,
		nr_box varchar(255) DEFAULT NULL,
		nr_cantine varchar(255) DEFAULT NULL,
		nr_locali varchar(255) DEFAULT NULL,
		note_locali varchar(255) DEFAULT NULL,
		nr_servizi varchar(255) DEFAULT NULL,
		nr_camere varchar(255) DEFAULT NULL,
		mq_giardino varchar(255) DEFAULT NULL,
		box_auto varchar(255) DEFAULT NULL,
		mansarda varchar(255) DEFAULT NULL,
		arredato varchar(255) DEFAULT NULL,
		giardino varchar(255) DEFAULT NULL,
		unita_immobiliari varchar(255) DEFAULT NULL,
		tipo_cucina varchar(255) DEFAULT NULL,
		condizionatore varchar(255) DEFAULT NULL,
		disponibilita varchar(255) DEFAULT NULL,
		numero_piano varchar(255) DEFAULT NULL,
		indirizzo varchar(255) DEFAULT NULL,
		latitudine varchar(255) DEFAULT NULL,
		longitudine varchar(255) DEFAULT NULL,
		titolo_ita text(0) DEFAULT NULL,
		titolo_eng text(0) DEFAULT NULL,
		titolo_fra text(0) DEFAULT NULL,
		titolo_ted text(0) DEFAULT NULL,
		titolo_spa text(0) DEFAULT NULL,
		descrizione_breve_ita text(0) DEFAULT NULL,
		descrizione_breve_eng text(0) DEFAULT NULL,
		descrizione_breve_fra text(0) DEFAULT NULL,
		descrizione_breve_ted text(0) DEFAULT NULL,
		descrizione_breve_spa text(0) DEFAULT NULL,
		descrizione_lunga_ita text(0) DEFAULT NULL,
		descrizione_lunga_eng text(0) DEFAULT NULL,
		descrizione_lunga_fra text(0) DEFAULT NULL,
		descrizione_lunga_ted text(0) DEFAULT NULL,
		descrizione_lunga_spa text(0) DEFAULT NULL,
      PRIMARY KEY id (id)
    );";
    
    $table_name = $wpdb->prefix . 'immagini_domusuite';
    
    $sql2 = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		id_immobile int(11) DEFAULT NULL,
		url text(0) DEFAULT NULL,
      PRIMARY KEY id (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql2 );
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
    //INSERIMENTO CRON
    
	wp_schedule_event( time(), 'hourly', 'cron_domusuite' );
	add_option('paging_domusuite',10);
}

add_action( 'cron_domusuite', 'aggiornamento_immobili_domusuite' );

function aggiornamento_immobili_domusuite() {
	//PROVA
	/*
	$headers = "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/html; charset=UTF-8\n";
	$headers .= "From: info@delphinet.it>\n";
	$headers .=  "To: alessio@delphinet.it\n";
	$headers .=  "X-Priority: 1\n";
	$headers .=  "X-MSMail-Priority: High\n";
	$headers .=  "X-Mailer: PHP Mail Server";

	mail("", 'Test invio cron', "Prova", $headers);
	*/
	aggiornamento_database_immobili();
	
}

function aggiornamento_database_immobili() {
	global $wpdb;
	if (get_option( 'url_xml_domusuite' ) !== false) {
		
		$xml_file = file_get_contents(get_option( 'url_xml_domusuite' ));
		$requestBodyXML = new DOMDocument();
		if ($requestBodyXML->loadXML($xml_file)) {
			$xml = simplexml_import_dom($requestBodyXML);
			
			if(count($xml->immobile)>0) {
				foreach($xml->immobile as $dati) {
					$table_name = $wpdb->prefix . 'immobili_domusuite';
					if ($dati->contratto=='Vendita') {
						$prezzo = $dati->prezzo_vendita;
					} else {
						$prezzo = $dati->prezzo_affitto;
					}
					
					$myrows = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$table_name." WHERE codice=%s", "'".$dati->codice."'") );
					if (count($myrows)>0) {
					
						$wpdb->update( 
						$table_name, 
							array( 
								'riferimento' => $dati->riferimento,
					            'categoria' => $dati->categoria,
					            'a_reddito' => $dati->a_reddito,
					            'reddito' => $dati->reddito,
					            'tipologia' => $dati->tipologia,
					            'trattativa' => $dati->trattativa,
					            'contratto' => $dati->contratto,
					            'prezzo' => $prezzo,
					            'canone_affitto' => $dati->canone_affitto,
					            'comune' => $dati->comune,
					            'provincia' => $dati->provincia,
					            'regione' => $dati->regione,
					            'nazione' => $dati->nazione,
					            'anno_costruzione' => $dati->anno_costruzione,
					            'mq_coperti' => $dati->mq_coperti,
					            'mq_terreno' => $dati->mq_terreno,
					            'balconi' => $dati->balconi,
					            'mq_balconi' => $dati->mq_balconi,
					            'terrazze' => $dati->terrazze,
					            'mq_terrazze' => $dati->mq_terrazze,
					            'cantine' => $dati->cantine,
					            'mq_cantine' => $dati->mq_cantine,
					            'box' => $dati->box,
					            'mq_box' => $dati->mq_box,
					            'p_auto_scoperto' => $dati->p_auto_scoperto,
					            'p_auto_coperto' => $dati->p_auto_coperto,
					            'liv_fuori_terra' => $dati->liv_fuori_terra,
					            'liv_interrati' => $dati->liv_interrati,
					            'portiere' => $dati->portiere,
					            'ascensore' => $dati->ascensore,
					            'descrizione_piano' => $dati->descrizione_piano,
					            'stato' => $dati->stato,
					            'ipe' => $dati->ipe,
					            'classe_energetica' => $dati->classe_energetica,
					            'riscaldamento' => $dati->riscaldamento,
					            'nr_balconi' => $dati->nr_balconi,
					            'nr_terrazze' => $dati->nr_terrazze,
					            'nr_box' => $dati->nr_box,
					            'nr_cantine' => $dati->nr_cantine,
					            'nr_locali' => $dati->nr_locali,
					            'note_locali' => $dati->note_locali,
					            'nr_servizi' => $dati->nr_servizi,
					            'nr_camere' => $dati->nr_camere,
					            'mq_giardino' => $dati->mq_giardino,
					            'box_auto' => $dati->box_auto,
					            'mansarda' => $dati->mansarda,
					            'arredato' => $dati->arredato,
					            'giardino' => $dati->giardino,
					            'unita_immobiliari' => $dati->unita_immobiliari,
					            'tipo_cucina' => $dati->tipo_cucina,
					            'condizionatore' => $dati->condizionatore,
					            'disponibilita' => $dati->disponibilita,
					            'numero_piano' => $dati->numero_piano,
					            'indirizzo' => $dati->indirizzo,
					            'latitudine' => $dati->latitudine,
					            'longitudine' => $dati->longitudine,
					            'titolo_ita' => $dati->titolo_ita,
					            'titolo_eng' => $dati->titolo_eng,
					            'titolo_fra' => $dati->titolo_fra,
					            'titolo_ted' => $dati->titolo_ted,
					            'titolo_spa' => $dati->titolo_spa,
					            'descrizione_breve_ita' => $dati->descrizione_breve_ita,
					            'descrizione_breve_eng' => $dati->descrizione_breve_eng,
					            'descrizione_breve_fra' => $dati->descrizione_breve_fra,
					            'descrizione_breve_ted' => $dati->descrizione_breve_ted,
					            'descrizione_breve_spa' => $dati->descrizione_breve_spa,
					            'descrizione_lunga_ita' => $dati->descrizione_lunga_ita,
					            'descrizione_lunga_eng' => $dati->descrizione_lunga_eng,
					            'descrizione_lunga_fra' => $dati->descrizione_lunga_fra,
					            'descrizione_lunga_ted' => $dati->descrizione_lunga_ted,
					            'descrizione_lunga_spa' => $dati->descrizione_lunga_spa
					            ), 
							array( 'id' => $myrows[0]->id ));
						$nuovo_id = $myrows[0]->id;
						
						$arr_id_trovati[] = $nuovo_id;
					} else {
						$wpdb->insert(
					        $table_name, 
					        array( 
					            'codice' => $dati->codice, 
					            'riferimento' => $dati->riferimento,
					            'categoria' => $dati->categoria,
					            'a_reddito' => $dati->a_reddito,
					            'reddito' => $dati->reddito,
					            'tipologia' => $dati->tipologia,
					            'trattativa' => $dati->trattativa,
					            'contratto' => $dati->contratto,
					            'prezzo' => $prezzo,
					            'canone_affitto' => $dati->canone_affitto,
					            'comune' => $dati->comune,
					            'provincia' => $dati->provincia,
					            'regione' => $dati->regione,
					            'nazione' => $dati->nazione,
					            'anno_costruzione' => $dati->anno_costruzione,
					            'mq_coperti' => $dati->mq_coperti,
					            'mq_terreno' => $dati->mq_terreno,
					            'balconi' => $dati->balconi,
					            'mq_balconi' => $dati->mq_balconi,
					            'terrazze' => $dati->terrazze,
					            'mq_terrazze' => $dati->mq_terrazze,
					            'cantine' => $dati->cantine,
					            'mq_cantine' => $dati->mq_cantine,
					            'box' => $dati->box,
					            'mq_box' => $dati->mq_box,
					            'p_auto_scoperto' => $dati->p_auto_scoperto,
					            'p_auto_coperto' => $dati->p_auto_coperto,
					            'liv_fuori_terra' => $dati->liv_fuori_terra,
					            'liv_interrati' => $dati->liv_interrati,
					            'portiere' => $dati->portiere,
					            'ascensore' => $dati->ascensore,
					            'descrizione_piano' => $dati->descrizione_piano,
					            'stato' => $dati->stato,
					            'ipe' => $dati->ipe,
					            'classe_energetica' => $dati->classe_energetica,
					            'riscaldamento' => $dati->riscaldamento,
					            'nr_balconi' => $dati->nr_balconi,
					            'nr_terrazze' => $dati->nr_terrazze,
					            'nr_box' => $dati->nr_box,
					            'nr_cantine' => $dati->nr_cantine,
					            'nr_locali' => $dati->nr_locali,
					            'note_locali' => $dati->note_locali,
					            'nr_servizi' => $dati->nr_servizi,
					            'nr_camere' => $dati->nr_camere,
					            'mq_giardino' => $dati->mq_giardino,
					            'box_auto' => $dati->box_auto,
					            'mansarda' => $dati->mansarda,
					            'arredato' => $dati->arredato,
					            'giardino' => $dati->giardino,
					            'unita_immobiliari' => $dati->unita_immobiliari,
					            'tipo_cucina' => $dati->tipo_cucina,
					            'condizionatore' => $dati->condizionatore,
					            'disponibilita' => $dati->disponibilita,
					            'numero_piano' => $dati->numero_piano,
					            'indirizzo' => $dati->indirizzo,
					            'latitudine' => $dati->latitudine,
					            'longitudine' => $dati->longitudine,
					            'titolo_ita' => $dati->titolo_ita,
					            'titolo_eng' => $dati->titolo_eng,
					            'titolo_fra' => $dati->titolo_fra,
					            'titolo_ted' => $dati->titolo_ted,
					            'titolo_spa' => $dati->titolo_spa,
					            'descrizione_breve_ita' => $dati->descrizione_breve_ita,
					            'descrizione_breve_eng' => $dati->descrizione_breve_eng,
					            'descrizione_breve_fra' => $dati->descrizione_breve_fra,
					            'descrizione_breve_ted' => $dati->descrizione_breve_ted,
					            'descrizione_breve_spa' => $dati->descrizione_breve_spa,
					            'descrizione_lunga_ita' => $dati->descrizione_lunga_ita,
					            'descrizione_lunga_eng' => $dati->descrizione_lunga_eng,
					            'descrizione_lunga_fra' => $dati->descrizione_lunga_fra,
					            'descrizione_lunga_ted' => $dati->descrizione_lunga_ted,
					            'descrizione_lunga_spa' => $dati->descrizione_lunga_spa
					            ) 
					        );
					        $nuovo_id = $wpdb->insert_id;
					        
					        $arr_id_trovati[] = $nuovo_id;
					}
					
					if (count($dati->immagini)>0) {	
						$wpdb->delete( $wpdb->prefix . 'immagini_domusuite', array( 'id_immobile' =>$nuovo_id ) );
						foreach ($dati->immagini->immagine as $immagine_singola) {
							$table_name = $wpdb->prefix . 'immagini_domusuite';
							$wpdb->insert(
			        			$table_name, 
			        			array( 
			        				'id_immobile' => $nuovo_id,
			            			'url' => $immagine_singola 
			              		) 
				        	);
						}
					}	
								
				}
				$table_name = $wpdb->prefix . 'immobili_domusuite';
				$myrows_del = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$table_name.""));
				if (count($myrows_del)>0) {
					foreach ($myrows_del as $row_singola_del) {
						if (!in_array($row_singola_del->id,$arr_id_trovati)) {
							$wpdb->delete( $wpdb->prefix . 'immobili_domusuite', array( 'id' =>$row_singola_del->id ));
							
							$wpdb->delete( $wpdb->prefix . 'immagini_domusuite', array( 'id_immobile' =>$row_singola_del->id ) );
						}
					}
				}
				
			}
			
		}
	
	}
}

register_activation_hook( __FILE__, 'attivazione_plugin_domusuite');


function page_admin_domusuite() {
	if ($_REQUEST["url_domusuite"]) {
		$sxe = simplexml_load_file($_REQUEST["url_domusuite"]);
		if (!$sxe) {
		    echo "Failed loading XML\n";
		} else {
			if (get_option( 'url_xml_domusuite' ) !== false ) {
				update_option('url_xml_domusuite', $_REQUEST["url_domusuite"]);
			} else {
				add_option('url_xml_domusuite', $_REQUEST["url_domusuite"]);
			}
			
			aggiornamento_database_immobili();
		}
	}
	echo '<h1>Impostazioni DomuSuite</h1>';
	echo '<form method="post">Inserisci il link al file xml: <input type="text" name="url_domusuite" value="'.get_option('url_xml_domusuite').'" /><input name="Invia" value="Registra" type="submit" /></form><br><br>Una volta caricata il link al file xml inserire lo shortcode <b>[lista-immobili-domusuite]</b> in una pagina di wordpress.';
	
}

function add_menu_admin_domusuite() {
	add_options_page('DomuSuite Options', 'DomuSuite Options', 'administrator', 'domusuite-options', 'page_admin_domusuite');
}

add_action('admin_menu', 'add_menu_admin_domusuite');

add_action( 'wp_enqueue_scripts', 'get_header_domusuite' );
function get_header_domusuite() {
	$gmaps_url = 'http://maps.googleapis.com/maps/api/js?key=' . $key . '&amp;sensor=false';
  	//wp_register_script('j-query','http://code.jquery.com/jquery-1.9.1.js',null,null);
  	wp_register_script('google-maps', $gmaps_url, NULL, NULL,false);
  	wp_register_script('prettyphoto',plugins_url( '/js/jquery.prettyPhoto.js', __FILE__ ),null,null);
	//wp_register_script( 'map_google', 'https://maps.googleapis.com/maps/api/js?v=3.exp&language=it' );
	wp_register_style( 'style_domusuite', plugins_url( '/css/style_domusuite.css', __FILE__ ), array(), '20141127', 'all' ); 
	wp_register_style( 'style_prettyphoto', plugins_url( '/css/prettyPhoto.css', __FILE__ ), array(), '20141127', 'all' ); 
	//wp_register_style( 'style_screen', plugins_url( '/css/screen.css', __FILE__ ), array(), '20141127', 'all' ); 
	wp_enqueue_script( 'jquery' ); 
	wp_enqueue_script( 'google-maps' ); 
	wp_enqueue_script( 'prettyphoto' ); 
	wp_enqueue_style( 'style_domusuite' ); 
	wp_enqueue_style( 'style_prettyphoto' ); 
	//wp_enqueue_style( 'style_screen' ); 
	
}

function get_lista_immobili_domusuite($atts) {
$key = get_option('google_maps_api_key');
  	
	global $wp_query,$wpdb,$post;
	
	if ($wp_query->get('codice_immobile')) {
		$variabile = $wp_query->query_vars["codice_immobile"];

		$arr_variabile = explode("_",$variabile);

		$id_immobile = $arr_variabile[(count($arr_variabile)-1)];
		
		if ($id_immobile!="") {
			$immobili = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."immobili_domusuite  WHERE id=%d",$id_immobile) );
		
			$ritorno = '';
	    	if(!empty($immobili)) {
	    		$immagine = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."immagini_domusuite WHERE id_immobile=%d ORDER BY id asc",$immobili[0]->id));
				$ritorno .= '<div id="dettaglio_immobile_domusuite">';
				
				
				$richiesta = '';
				$richiesta .= "&search_contratto=".$_GET["search_contratto"];
				$richiesta .= "&search_tipologia=".$_GET["search_tipologia"];
				$richiesta .= "&search_citta=".$_GET["search_citta"];
				$richiesta .= "&search_prezzo=".$_GET["search_prezzo"];
				$richiesta .= "&search_page=".$_GET["search_page"];
				$richiesta .= "&order=".$_GET["order"];
				$link = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_content like '%%%s%%'", "[lista-immobili-domusuite"));
		
				
				$ritorno .= '<div class="torna_lista"></div>';
				$ritorno .= '<h1 class="titolo"><a href="/index.php?page_id='.$link[0]->ID.$richiesta.'">Elenco</a> > '.$immobili[0]->titolo_ita.'</h1>';
				$ritorno .= '<div class="codice"><span style="font-weight:bold;">Riferimento:</span> '.$immobili[0]->riferimento.$immobili[0]->codice.'</div>';
				
				$ritorno .= '<div class="immagine">';
				$ritorno .= '<a href="'.$immagine[0]->url.'" rel="prettyPhoto[gallery2]" title="'.$immobili[0]->titolo_ita.'" ><img src="'.$immagine[0]->url.'" /></a>';
				
				$ritorno .= '</div>';
				$ritorno .= '<div class="thumb">';
				foreach ($immagine as $chiave_immagine => $immagine_singola) {
					if ($chiave_immagine!=0) {
						$ritorno .= '<div class="thumb_singola">';
						$ritorno .= '<a href="'.$immagine_singola->url.'" rel="prettyPhoto[gallery2]" title="'.$immobili[0]->titolo_ita.'" ><img src="'.$immagine_singola->url.'" /></a>';
						$ritorno .= '</div>';
					}
				}
				$ritorno .= '</div>';
				
				$ritorno .= '<div class="altre_info">';
	    		$ritorno .= '<div class="prezzo">€ '.number_format($immobili[0]->prezzo,0,',','.').'</div>';
	    		$ritorno .= '<div class="mq">'.$immobili[0]->mq_coperti.' Mq</div>';
	    		if ($immobili[0]->nr_locali!='') {
	    			$ritorno .= '<div class="locali">'.$immobili[0]->nr_locali.' Locali</div>';
	    		}
	    		$ritorno .= '</div>';
	    		$ritorno .= '<div class="spazio_altre_info"></div>';
				
				if ($immobili[0]->tipologia!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Tipologia</div><div class="testo">'.ucfirst(strtolower($immobili[0]->tipologia)).'</div></div>';
				}
				if ($immobili[0]->contratto!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Contratto</div><div class="testo">'.ucfirst(strtolower($immobili[0]->contratto)).'</div></div>';
				}
				if ($immobili[0]->nr_servizi!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Bagni</div><div class="testo">'.$immobili[0]->nr_servizi.'</div></div>';
				}
				if ($immobili[0]->riscaldamento!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Riscaldamento</div><div class="testo">'.ucfirst(strtolower($immobili[0]->riscaldamento)).'</div></div>';
				}
				if ($immobili[0]->tipo_cucina!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Cucina</div><div class="testo">'.ucfirst(strtolower($immobili[0]->tipo_cucina)).'</div></div>';
				}
				if ($immobili[0]->nr_terrazze!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Terrazzi</div><div class="testo">'.$immobili[0]->nr_terrazze.'</div></div>';
				}
				if ($immobili[0]->condizionatore!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Condizionatore</div><div class="testo">'.ucfirst(strtolower($immobili[0]->condizionatore)).'</div></div>';
				}
				if ($immobili[0]->nr_balconi!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Balconi</div><div class="testo">'.$immobili[0]->nr_balconi.'</div></div>';
				}
				if ($immobili[0]->numero_piano!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Piano</div><div class="testo">'.ucfirst(strtolower($immobili[0]->numero_piano)).'</div></div>';
				}
				if ($immobili[0]->ascensore!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Ascensore</div><div class="testo">'.ucfirst(strtolower($immobili[0]->ascensore)).'</div></div>';
				}
				if ($immobili[0]->nr_box!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Box</div><div class="testo">'.$immobili[0]->nr_box.'</div></div>';
				}
				if ($immobili[0]->giardino!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Giardino</div><div class="testo">'.ucfirst(strtolower($immobili[0]->giardino)).'</div></div>';
				}
				if ($immobili[0]->stato!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Stato</div><div class="testo">'.ucfirst(strtolower($immobili[0]->stato)).'</div></div>';
				}
				if ($immobili[0]->arredato!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Arredamento</div><div class="testo">'.ucfirst(strtolower($immobili[0]->arredato)).'</div></div>';
				}
				if ($immobili[0]->cantine!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Cantina</div><div class="testo">'.ucfirst(strtolower($immobili[0]->cantine)).'</div></div>';
				}
				if ($immobili[0]->classe_energetica!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Classe energetica</div><div class="testo">'.$immobili[0]->classe_energetica." ".$immobili[0]->ipe.'</div></div>';
				}
				if ($immobili[0]->a_reddito!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">A reddito</div><div class="testo">'.ucfirst(strtolower($immobili[0]->a_reddito)).'</div></div>';
				}
				if ($immobili[0]->spese_condominiali!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Spese condominiali</div><div class="testo">'.ucfirst(strtolower($immobili[0]->spese_condominiali)).'</div></div>';
				}
				if ($immobili[0]->anno_costruzione!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Anno di costruz.</div><div class="testo">'.$immobili[0]->anno_costruzione.'</div></div>';
				}
				if ($immobili[0]->p_auto_coperti!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Posti auto coperti</div><div class="testo">'.$immobili[0]->p_auto_coperti.'</div></div>';
				}
				if ($immobili[0]->p_auto_scoperti!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Posti auto scoperti</div><div class="testo">'.$immobili[0]->p_auto_scoperti.'</div></div>';
				}
				if ($immobili[0]->portiere!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Portiere</div><div class="testo">'.ucfirst(strtolower($immobili[0]->portiere)).'</div></div>';
				}
				if ($immobili[0]->mansarda!='') {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Mansarda</div><div class="testo">'.ucfirst(strtolower($immobili[0]->mansarda)).'</div></div>';
				}
				
				if ($immobili[0]->unita_immobiliari!=''&&$immobili[0]->unita_immobiliari!=0) {
					$ritorno .= '<div class="info_immobile"><div class="titolo">Unit&agrave; immobiliari</div><div class="testo">'.$immobili[0]->unita_immobiliari.'</div></div>';
				}
				
				$ritorno .= '<div class="spazio_blocco"></div>';
				$ritorno .= '<div class="descrizione"><span style="font-weight:bold;">Descrizione</span><br>'.ucfirst(strtolower($immobili[0]->descrizione_lunga_ita)).'</div>';
				$ritorno .= '<div class="spazio_blocco"></div>';
				$ritorno .= '<div class="indirizzo"><span style="font-weight:bold;">Indirizzo:</span> '.$immobili[0]->indirizzo.', '.$immobili[0]->comune;
				if ($immobili[0]->zona!='') {
					$ritorno .= ' ('.$immobili[0]->zona.')';
				}
				$ritorno .= '</div>';
				//INSERIRE MAPPA
				$ritorno .= '<script type="text/javascript">'.chr(13);
				$ritorno .= 'jQuery("a[rel^=\'prettyPhoto\']").prettyPhoto();'.chr(13);
				$ritorno .= 'function initialize() {'.chr(13);
				$ritorno .= 'var mapOptions = {'.chr(13);
				//$ritorno .= 'scaleControl: true,'.chr(13);
				$ritorno .= 'center: new google.maps.LatLng('.$immobili[0]->latitudine.', '.$immobili[0]->longitudine.'),'.chr(13);
				$ritorno .= 'zoom: 16'.chr(13);
				$ritorno .= '};'.chr(13);
				
				$ritorno .= 'var map = new google.maps.Map(document.getElementById(\'mappa_immobile_domusuite\'),'.chr(13);
				$ritorno .= 'mapOptions);'.chr(13);
				
				$ritorno .= 'var marker = new google.maps.Marker({'.chr(13);
				$ritorno .= 'map: map,'.chr(13);
				$ritorno .= 'position: map.getCenter()'.chr(13);
				$ritorno .= '});'.chr(13);
				$ritorno .= 'var infowindow = new google.maps.InfoWindow();'.chr(13);
				//$ritorno .= 'infowindow.setContent(\'<b>aaa</b>\');'.chr(13);
				//$ritorno .= 'google.maps.event.addListener(marker, \'click\', function() {'.chr(13);
				//$ritorno .= 'infowindow.open(map, marker);'.chr(13);
				//$ritorno .= '});'.chr(13);
				$ritorno .= '}'.chr(13);
				$ritorno .= 'google.maps.event.addDomListener(window, \'load\', initialize);'.chr(13);
				
				$ritorno .= '</script>';
				$ritorno .= '<div id="mappa_immobile_domusuite"></div>';
				$ritorno .= '</div>';
				
				
			}
		}
	} else {
		extract(shortcode_atts(array('tipo' => '0'), $atts));
		if (get_option( 'paging_domusuite' ) !== false) {
			$limit = get_option( 'paging_domusuite' );
		} else {
			$limit = 2;
		}
		//$limit = 2;
		echo '<h3>Lista immobili</h3>';
		if ($_GET["search_page"]!='') {
			$pagina = $_GET["search_page"];
		} else {
			$pagina = 1;
		}
		
		$Qinizio = ($pagina-1)*$limit;
		
		//LEGGERE DB E CREARE LISTA (CON FILTRI)
		$rFiltri = '';
		$prepare = array();
		if ($_GET["search_contratto"]!='') {
			$rFiltri .= " AND contratto=%s ";
			$prepare[] = $_GET["search_contratto"];
		}
		if ($_GET["search_tipologia"]!='') {
			$rFiltri .= " AND tipologia=%s ";
			$prepare[] = $_GET["search_tipologia"];
		}
		if ($_GET["search_citta"]!='') {
			$rFiltri .= " AND comune=%s ";
			$prepare[] = $_GET["search_citta"];
		}
		if ($_GET["search_prezzo"]!=''&&is_numeric($_GET["search_prezzo"])) {
			$rFiltri .= " AND CAST(prezzo AS UNSIGNED)<=%f ";
			$prepare[] = $_GET["search_prezzo"];
		}
		
		$rOrder = ' ORDER BY CONVERT(prezzo,UNSIGNED INTEGER)  asc';
		if ($_GET["order"]!='') {
			switch ($_GET["order"]) {
				case(1):
					$rOrder = ' ORDER BY CONVERT(prezzo,UNSIGNED INTEGER)  asc';
					break;
				case(2):
					$rOrder = ' ORDER BY CONVERT(prezzo,UNSIGNED INTEGER) desc';
					break;
				case(3):
					$rOrder = ' ORDER BY CONVERT(mq_coperti,UNSIGNED INTEGER) asc';
					break;
				case(4):
					$rOrder = ' ORDER BY CONVERT(mq_coperti,UNSIGNED INTEGER) desc';
					break;
			}
		}
		
		$immobili = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."immobili_domusuite  WHERE 1=1 ".$rFiltri.$rOrder." LIMIT ".$Qinizio.",".$limit,$prepare));
		
		$immobili_count = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."immobili_domusuite  WHERE 1=1 ".$rFiltri,$prepare));
		
		$ritorno = '';
	    if(!empty($immobili)) {
	    	$ritorno .= '<div id="lista_immobili_domusuite">';
	    	$richiesta = '';
			$richiesta .= "&search_contratto=".$_GET["search_contratto"];
			$richiesta .= "&search_tipologia=".$_GET["search_tipologia"];
			$richiesta .= "&search_citta=".$_GET["search_citta"];
			$richiesta .= "&search_prezzo=".$_GET["search_prezzo"];
	    	$ritorno .= '<div class="ordinamento">Ordina : <select onchange="location.href=\''.get_site_url().''.strtok($_SERVER["REQUEST_URI"],'?').'?search_page=1&order=\'+this.value+\''.$richiesta.'\'">';
	    	$ritorno .= '<option value="1" ';
	    	if ($_GET["order"]==1) {
	    		$ritorno.= ' selected ';
	    	}
	    	$ritorno .= '>Per prezzo minore</option>';
	    	$ritorno .= '<option value="2" ';
	    	if ($_GET["order"]==2) {
	    		$ritorno.= ' selected ';
	    	}
	    	$ritorno .= '>Per prezzo maggiore</option>';
	    	$ritorno .= '<option value="3" ';
	    	if ($_GET["order"]==3) {
	    		$ritorno.= ' selected ';
	    	}
	    	$ritorno .= '>Per superficie minore</option>';
	    	$ritorno .= '<option value="4" ';
	    	if ($_GET["order"]==4) {
	    		$ritorno.= ' selected ';
	    	}
	    	$ritorno .= '>Per superficie maggiore</option>';
	    	$ritorno .= '</select></div>';
	    	foreach ($immobili as $chiave => $immobile) {
	    		$immagine = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."immagini_domusuite WHERE id_immobile=%d ORDER BY id asc",$immobile->id));
	    		$ritorno .= '<div class="immobile">';
	    		$richiesta = '';
				$richiesta .= "search_contratto=".$_GET["search_contratto"];
				$richiesta .= "&search_tipologia=".$_GET["search_tipologia"];
				$richiesta .= "&search_citta=".$_GET["search_citta"];
				$richiesta .= "&search_prezzo=".$_GET["search_prezzo"];
				$richiesta .= "&search_page=".$_GET["search_page"];
				$richiesta .= "&order=".$_GET["order"];
	    		$ritorno .= '<div class="titolo"><a href="/immobile-domusuite/'.str_replace(" ","_",$immobile->titolo_ita).'_'.$immobile->id.'?'.$richiesta.'">'.ucfirst(strtolower($immobile->titolo_ita)).'</a></div>';
	    		$ritorno .= '<div class="immagine">';
	    		if(!empty($immagine)) {
	    			$ritorno .= '<a href="/immobile-domusuite/'.str_replace(" ","_",$immobile->titolo_ita).'_'.$immobile->id.'?'.$richiesta.'"><img src="'.$immagine[0]->url.'" /></a>';
	    		}
	    		$ritorno .= '</div>';
	    		$ritorno .= '<div class="informazioni">';
	    		$ritorno .= '<div class="tipologia">'.ucfirst(strtolower($immobile->tipologia)).' in '.ucfirst(strtolower($immobile->contratto)).'</div>';
	    		if ($immobile->zona!='') {
	    			$ritorno .= '<div class="comune">'.$immobile->comune.' ('.$immobile->zona.')</div>';
	    		} else {
	    			$ritorno .= '<div class="comune">'.$immobile->comune.'</div>';
	    		}
	    		$ritorno .= '<div class="descrizione_breve">'.ucfirst(strtolower($immobile->descrizione_breve_ita)).'</div>';
	    		$ritorno .= '<div class="altre_info">';
	    		$ritorno .= '<div class="prezzo">€ '.number_format($immobile->prezzo,0,',','.').'</div>';
	    		$ritorno .= '<div class="mq">'.$immobile->mq_coperti.' Mq</div>';
	    		if ($immobile->nr_locali!='') {
	    			$ritorno .= '<div class="locali">'.$immobile->nr_locali.' Locali</div>';
	    		}
	    		
	    		$ritorno .= '</div>';
	    		$ritorno .= '</div>';
	    		$ritorno .= '</div>';
	    	}
	    	$ritorno .= '</div>';
	    	
	    	$ritorno .= get_pagination_domusuite(count($immobili_count),$pagina,$limit);
	    }

	}
	    
    echo $ritorno;
}

add_shortcode( 'lista-immobili-domusuite', 'get_lista_immobili_domusuite' );

function get_pagination_domusuite($conteggio,$pagina,$limit) {
	$ritorno = '';
	
	$richiesta = '';
	$richiesta .= "&search_contratto=".$_GET["search_contratto"];
	$richiesta .= "&search_tipologia=".$_GET["search_tipologia"];
	$richiesta .= "&search_citta=".$_GET["search_citta"];
	$richiesta .= "&search_prezzo=".$_GET["search_prezzo"];
	$richiesta .= "&order=".$_GET["order"];
	if ($conteggio>$limit) {
		$ritorno .= '<div id="paginatore_domusuite">';
		
		$ritorno .= '<div class="pagina first">';
		if ($pagina>1) {
			$ritorno .= '<a href="'.get_site_url().''.strtok($_SERVER["REQUEST_URI"],'?').'?search_page=1'.$richiesta.'"><<</a>';
		} else {
			$ritorno .= '&nbsp;';
		}
		$ritorno .= '</div>';
	
		$ritorno .= '<div class=" pagina prev">';
		if ($pagina>1) {
			$ritorno .= '<a href="'.get_site_url().''.strtok($_SERVER["REQUEST_URI"],'?').'?search_page='.($pagina-1).$richiesta.'"><</a>';
		} else {
			$ritorno .= '&nbsp;';
		}
		$ritorno .= '</div>';
		
		if ($conteggio>0) {
			for ($a = 1;$a<=ceil($conteggio/$limit);$a++) {
				$ritorno .= '<div class="pagina num"><a href="'.get_site_url().''.strtok($_SERVER["REQUEST_URI"],'?').'?search_page='.($a).$richiesta.'">'.$a.'</a></div>';
			}
		}
		
		$ritorno .= '<div class="pagina next">';
		if ($pagina<ceil($conteggio/$limit)) {		
			$ritorno .= '<a href="'.get_site_url().''.strtok($_SERVER["REQUEST_URI"],'?').'?search_page='.($pagina+1).$richiesta.'">></a>';
		}
		$ritorno .= '</div>';
	
		$ritorno .= '<div class="pagina last">';
		if ($pagina<ceil($conteggio/$limit)) {		
			$ritorno .= '<a href="'.get_site_url().''.strtok($_SERVER["REQUEST_URI"],'?').'?search_page='.ceil($conteggio/$limit).$richiesta.'">>></a>';
		}
		$ritorno .= '</div>';
		
		
		$ritorno .= '</div>';
	}
	
	return $ritorno;
}

function add_dettaglio_url()
{
	global $wp,$wp_rewrite,$wpdb;
	$immagine = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_content like '%%%s%%'", "[lista-immobili-domusuite"));
	
	if (count($immagine)>0) {
		add_rewrite_rule(
        '^immobile-domusuite/(.*)/?$',
        'index.php?page_id='.$immagine[0]->ID.'&codice_immobile=$matches[1]',
        'top'
    	);
     	$wp_rewrite->flush_rules();
	}
    
    
    //print_r($wp_rewrite);
}

add_action('init', 'add_dettaglio_url');

function domusuite_query_vars( $query_vars ){
    $query_vars[] = 'codice_immobile';
    return $query_vars;
}
add_filter( 'query_vars', 'domusuite_query_vars' );


function disattivazione_plugin_domusuite() {
	delete_option('url_xml_domusuite');
	delete_option('paging_domusuite');
	
	global $wpdb;
	
	//ELIMINAZIONE DB 
	$table_name = $wpdb->prefix . 'immobili_domusuite';
	$wpdb->query("DROP TABLE {$table_name}");
	$table_name = $wpdb->prefix . 'immagini_domusuite';
	$wpdb->query("DROP TABLE {$table_name}");
	
	
	//ELIMINAZIONE CRON
	wp_clear_scheduled_hook('cron_domusuite');
	
	
}

register_deactivation_hook(__FILE__, 'disattivazione_plugin_domusuite');

?>