(function($){
    METAMATERIAL.UTILS = {

        incrementIndex : function($container, $the_name, $index){

            var $the_props = ['name', 'id', 'for', 'class'];
            var $reg = new RegExp('\\['+$the_name+'\\]\\[(\\d+)\\]', 'i');
            var $reg2 =/-n(\d+)/gi;
            var $firstmatch =false;
            $container.find('.mm_loop *').addClass('mm_ignore');
            $container.find('*').each(function(i, elem)
            {
                var $elem = $(elem);

                for (var j = 0; j < $the_props.length; j++)
                {
                    var $the_prop = $elem.attr($the_props[j]);

                    if ($the_prop)
                    {

                        var the_match = $the_prop.match($reg);

                        if (the_match)
                        {
                            if(!$firstmatch){ $firstmatch = the_match[1];}
                            var $newindex  = typeof $index !== 'undefined' ? $index : (+the_match[1]+1);

                            $the_prop = $the_prop.replace(the_match[0], '['+ $the_name + ']' + '['+ (+$newindex) +']');

                            $elem.attr($the_props[j], $the_prop);
                        }

                        the_match = null;
                        if(!$elem.hasClass('mm_ignore')){

                            $the_prop = $the_prop.replace($reg2,function(match, contents) {
                                var $newindex  = typeof $index !== 'undefined' ? $index : (+contents+1);
                                return '-n' + (+$newindex);
                            });
                            $elem.attr($the_props[j], $the_prop);
                        }else{
                            $elem.removeClass('mm_ignore');
                        }

                    }
                }
            });

            return $firstmatch;

        },

        process_ajax : function($response, $trigger, $metabox, $on_success, $on_error){
            //error
            if( !$response.success )
            {
                if($on_error){
                    var $error_handler = window[$on_error];
                    if(typeof $error_handler === 'function') {
                        if(!$error_handler($response, $trigger, $metabox)){
                            return true;
                        }
                    }

                }

                return METAMATERIAL.UTILS.display_ajax_alert( $response.data.error ,'error', $metabox);
            }

            //success
            if($on_success){
                var $success_handler = window[$on_success];
                if(typeof $success_handler === 'function') {
                    if(!$success_handler($response,$trigger, $metabox)){
                        return true;
                    }
                }
            }

            return METAMATERIAL.UTILS.display_ajax_alert( $response.data.message ,'success', $metabox);

        },

        display_ajax_alert : function ($message, $type, $metabox){
            var $class= $type=='error'?'error':($type=='success'?'updated':'update-nag');
            $metabox.find('.mm_ajax_notice').show().html('<div class="' + $class + '"><p>' + $message + '</p></div>').delay(4000).fadeOut();
        },

        checkLoopLimit : function(name,$context)
        {
            var elems = $('.mm_docopy-' + name, $context);

            $.each(elems, function(){

                var p = $(this).parents('.mm_group:first');

                if(p.length <= 0)
                    p = $(this).parents('.postbox');


                var the_limit = $('.mm_loop-' + name, p).data('mm_loop_limit');
                if(the_limit){
                    if ($('.mm_group-' + name, p).not('.mm_group.mm_tocopy').length >= the_limit)
                    {
                        $(this).hide();
                    }
                    else
                    {
                        $(this).show();
                    }
                }

            });
        }

    };
})(jQuery);