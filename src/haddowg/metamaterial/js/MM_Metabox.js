
if(typeof window.METAMATERIAL === 'undefined'){
	window.METAMATERIAL = {};
}
var METAMATERIAL = window.METAMATERIAL;

METAMATERIAL.MM_METABOX = {

	//runs after all document loads, i.e after standard wordpress js initialisation.
	onLoad: function($)
			{
				//prevent collapse toggle for ALWAYS_OPEN metaboxes
				$('.postbox.open > h3, .postbox.open .hndle').unbind('click.postboxes');

			},

	onReady: function($){
		//hide screen options
		$('.postbox.hide-screen-option').each(function(){
			$('.metabox-prefs label[for='+ $(this).attr('id') +'-hide]').remove();
		});

		//remove titles for headless metaboxes
		$('.postbox.headless > h3, .postbox.headless > .handlediv').remove();

		//remove toggle div for ALWAYS_OPEN metaboxes
		$('.postbox.open .handlediv').remove();

		//prevent dragging of locked metaboxes
		$('.postbox.locked > h3').removeClass('hndle');

		//move before_title metaboxes
		$('#before_title-sortables').prependTo('#post-body-content');

		$('.mm_loop[data-mm_sortable="true"]').sortable();

		$(document).on('click', '[class*=mm_dodelete]', function(e){

			e.preventDefault();

			var $this = $(this).first();
			var $p = $this.closest('.mm_group, .postbox');

			var $the_name = $this.attr('class').match(/mm_dodelete-([a-zA-Z0-9_-]*)/i);

			$the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;

			if(!$the_name && $p.hasClass('postbox')){
				return false;
			}

			var $conf = ($this.attr('data-mm_delete_confirm')==='false')?$this.data('mm_delete_confirm'):$this.data('mm_delete_confirm')||METAMATERIAL.LANG.DEFAULT_DELETE_CONFIRM;

			var $proceed = ($conf!==false)?confirm($conf):true;

			if ($proceed)
			{
				var $context;

				if ($the_name)
				{
					$context = $p;
					$('.mm_group-'+ $the_name, $p).not('.mm_tocopy').remove();
				}
				else
				{
					$context = $p.parents('.mm_group, .postbox').first();
					$p.remove();
				}

				if(!$the_name)
				{
					var $the_group = $this.closest('.mm_group');
					if($the_group && $the_group.attr('class'))
					{
						$the_name = $the_group.attr('class').match(/mm_group-([a-zA-Z0-9_-]*)/i);
						$the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;
					}
				}
				METAMATERIAL.UTILS.checkLoopLimit($the_name,$context);
				$context.trigger('mm_delete.'+$the_name, $the_name);
				return true;
			}
			return false;
		});

		$(document).on('click', '[class*=mm_docopy-]',function(e)
		{
			e.preventDefault();

			var $this = $(this).first();

			var $p = $this.closest('.mm_group, .postbox');

			var $the_name = $this.attr('class').match(/mm_docopy-([a-zA-Z0-9_-]*)/i)[1];

			var $the_group = $('.mm_group-'+ $the_name +'.mm_tocopy', $p).first();

			var $the_clone = $the_group.clone().removeClass('mm_tocopy last');

			METAMATERIAL.UTILS.incrementIndex($the_group, $the_name);

			if ($this.hasClass('ontop'))
			{
				$('.mm_group-'+ $the_name, $p).first().before($the_clone);
			}
			else
			{
				$the_group.before($the_clone);
			}

			METAMATERIAL.UTILS.checkLoopLimit($the_name);
			$the_clone.trigger('mm_copy',[$the_name,$the_clone]);
			$the_clone.trigger('mm_copy.'+$the_name, $the_name);
		});

		$(document).on('click', '[class*=mm_dodupe]',function(e)
		{
			e.preventDefault();

			var $this = $(this).first();

			var $p = $this.closest('.mm_group');

			if($p.length < 1){
				return false;
			}

			var $the_clone = $p.clone().removeClass('first');

			var $the_name = $p.attr('class').match(/mm_group-([a-zA-Z0-9_-]*)/i);
			$the_name = ($the_name && $the_name[1]) ? $the_name[1] : null ;

			if(!$the_name){
				return false;
			}

			var $the_group = $('.mm_group-'+ $the_name +'.mm_tocopy', $p.parent()).first();

			var $index = METAMATERIAL.UTILS.incrementIndex($the_group, $the_name);
			METAMATERIAL.UTILS.incrementIndex($the_clone, $the_name, $index);

			if ($this.hasClass('ontop'))
			{
				$('.mm_group-'+ $the_name, $p).first().before($the_clone);
			}
			else
			{
				$the_group.before($the_clone);
			}

			METAMATERIAL.UTILS.checkLoopLimit($the_name);

			$the_clone.trigger('mm_dupe.'+$the_name, $the_name);
			return true;
		});

		$(document).on('click', '[class*=mm_doajax]',function(e)
		{
			e.preventDefault();

			var $metabox = $(this).closest('.postbox');
			METAMATERIAL.UTILS.clear_ajax_alert($metabox);
			var $mm_id = $metabox.attr('id').match(/([a-zA-Z0-9_-]*?)_metamaterial/i);
			$mm_id = ($mm_id && $mm_id[1]) ? $mm_id[1] : null ;
			if(!$mm_id){
				return false;
			}
			var $pt = $('#post_type').eq(0).val();
			var $pid = $('#post_ID').eq(0).val();
			var $fields = $('[name^="' + $mm_id + '"]' ,$metabox);
			var $action = $(this).data('mm_ajax_action') || 'ajax_save';
			var $on_success = $(this).data('mm_on_success');
			var $on_error = $(this).data('mm_on_error');
			var $data = {
				action: 'metamaterial_action_'+ $mm_id + '_' + $action,
				mm_object_id: $pid,
				mm_nonce: $('[name="'+ $mm_id + '_nonce"]',$metabox).eq(0).val(),
				post_type: $pt
			};
			$data = $.param($data) + '&' + $fields.serialize();
			$.post(ajaxurl,$data, function($response){
				return METAMATERIAL.UTILS.process_ajax($response, $(this), $metabox, $on_success,$on_error);
			});
		});



		/* do an initial limit check, show or hide buttons */
		$('[class*=mm_docopy-]').each(function()
		{
			var $the_name = $(this).attr('class').match(/mm_docopy-([a-zA-Z0-9_-]*)/i)[1];

			METAMATERIAL.UTILS.checkLoopLimit($the_name);
		});
	}
};


//runs after all document loads, i.e after standard wordpress js initialisation.
jQuery(window).load(METAMATERIAL.MM_METABOX.onLoad(jQuery));

//on document ready
jQuery(document).ready(METAMATERIAL.MM_METABOX.onReady(jQuery));