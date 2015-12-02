$(function() {
	$( "#search_clfoco" ).autocomplete({
		source: "../../modules/root/search.php",
		minLength: 2,
        html: true, // optional (jquery.ui.autocomplete.html.js required)
 
      // optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
		select: function(event, ui) {
			$("#search_clfoco").val(ui.item.value);
			$(this).closest("form").submit();
		}
	});
	$( "#search_id_customer" ).autocomplete({
		source: "../../modules/root/search.php",
		minLength: 2,
        html: true, // optional (jquery.ui.autocomplete.html.js required)
 
      // optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
		select: function(event, ui) {
			$("#search_id_customer").val(ui.item.value);
			$(this).closest("form").submit();
		}
	});
	$( "#search_cosear" ).autocomplete({
		source: "../../modules/root/search.php?opt=product",
		minLength: 2,
        html: true, // optional (jquery.ui.autocomplete.html.js required)
 
      	// optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
		select: function(event, ui) {
			$("#search_cosear").val(ui.item.value);
			$(this).closest("form").submit();
		}
	});
	$( "#search_location" ).autocomplete({
		source: "../../modules/root/search.php?opt=location",
		minLength: 2,
		html: true, // optional (jquery.ui.autocomplete.html.js required)
		focus: function( event, ui ) {
			$( "#search_location" ).val( ui.item.value );
			$( "#search_location-capspe" ).val( ui.item.id );
			$( "#search_location-prospe" ).val( ui.item.prospe );
			$( "#country").val( ui.item.country );
			return false;
		},
		select: function( event, ui ) {
			$( "#search_location" ).val( ui.item.value );
			$( "#search_location-capspe" ).val( ui.item.id ); /* era capspe che è uguale a id, inutile duplicare un elemento dell'array*/
			$( "#search_location-prospe" ).val( ui.item.prospe );
			$( "#country").val( ui.item.country );  //grazie ad Emanuele Ferrarini
			return false;
		}
	});
	$('#search_location').blur(function() {
		if( !$(this).val() ) {
			$( "#search_location-capspe" ).val("");
			$( "#search_location-prospe" ).val("");
			$( "#country").val("IT");
		}
	});
	
	$( "#search_luonas" ).autocomplete({
		source: "../../modules/root/search.php?opt=location",
		minLength: 2,
		html: true, // optional (jquery.ui.autocomplete.html.js required)
		focus: function( event, ui ) {
			$( "#search_luonas" ).val( ui.item.value );
			$( "#search_pronas" ).val( ui.item.prospe );
			$( "#cuonas").val( ui.item.country );
			return false;
		},
		select: function( event, ui ) {
			$( "#search_luonas" ).val( ui.item.value );
			$( "#search_pronas" ).val( ui.item.prospe );
			$( "#cuonas").val( ui.item.country );  //grazie ad Emanuele Ferrarini
			return false;
		}
	});
	$('#search_luonas').blur(function() {
		if( !$(this).val() ) {
			$( "#search_pronas" ).val("");
			$( "#cuonas").val("IT");
		}
	});
});

