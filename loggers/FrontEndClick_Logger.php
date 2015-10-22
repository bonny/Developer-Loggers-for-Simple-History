<?php

class FrontEndClick_Logger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        return array(
            "name" => "FrontEndClick_Logger",
            "description" => "Logs clicks on stuff on frontend",
            "capability" => "manage_options",
            "messages" => array(
                "clicked" => __( 'Click detected', "simple-history" ),
            ),
            "labels" => array(),
        );

    }

    function loaded() {

        add_action("wp_enqueue_scripts", function() {
            wp_enqueue_script( "jquery" );
        });

        add_action( "wp_footer", array( $this, "add_script" ) );

    }

    function add_script() {

        ?>
        <script>

            (function($) {

                var ajaxURL = "<?php echo admin_url( 'admin-ajax.php' ) ?>";
                var selector = "a";

                function collectClick(e) {

                    console.log("click detected", e);
                    var $target = $(e.target);
                    var href = $target.attr("href");
                    var text = $target.attr("innerText");

                    $.post(ajaxURL, {
                        action: "<?php $this->slug ?>_collect_click",
                        href: href,
                        text: text
                    });

                }

                // todo: finnish this
                // $(document).on("click", selector, collectClick)

            })(jQuery);

        </script>
        
        <?php

    }

}
