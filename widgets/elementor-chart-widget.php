<?php
/*
 * Elementor ElementorChart Chart Widget
 * Author & Copyright: wpoceans
*/

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Elementor_Chart_Widget extends \Elementor\Widget_Base {

	/**
	 * Retrieve the widget name.
	*/
	public function get_name(){
		return 'ChartWidget';
	}

	/**
	 * Retrieve the widget title.
	*/
	public function get_title(){
		return esc_html__( 'Chart', 'elementorchart' );
	}

	/**
	 * Retrieve the widget icon.
	*/
	public function get_icon() {
		return 'eicon-countdown';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	*/
	public function get_categories() {
		return [ 'general', 'chartcategory' ];
	}

	/**
	 * Retrieve the list of scripts the ElementorChart Chart widget depended on.
	 * Used to set scripts dependencies required to run the widget.
	*/
	/*
	public function get_script_depends() {
		return ['wpo-elementorchart_chart'];
	}
	*/

	/**
	 * Register ElementorChart Chart widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	*/
	protected function register_controls(){

		$this->start_controls_section(
			'section_Chart',
			[
				'label' => esc_html__( 'Chart Options', 'elementorchart' ),
			]
		);

		$this->add_control(
      'chart_style',
      [
        'label' => esc_html__( 'Chart Style', 'elementorchart' ),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => [
            'doughnut' => esc_html__('Doughnut', 'elementorchart'),
            'bar' => esc_html__('Bar', 'elementorchart'),
        ],
        'default' => 'doughnut',
      ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'chart_title',
			[
				'label' => esc_html__( 'Title', 'elementorchart' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Chart item', 'elementorchart' ),
				'placeholder' => esc_html__( 'Type title text here', 'elementorchart' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'chart_value',
			[
				'label' => esc_html__( 'Value', 'elementorchart' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 50,
			]
		);

		$repeater->add_control(
			'chart_color',
			[
				'label' => esc_html__( 'Cart Color', 'elementorchart' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => 'purple'
			]
		);

		$repeater->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementorchart' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'purple'
			]
		);

		$this->add_control(
			'chart_group',
			[
				'label' => esc_html__( 'Chart Group', 'elementorchart' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'default' => [
					[
						'chart_title' => esc_html__( 'Chart', 'elementorchart' ),
					],

				],
				'fields' =>  $repeater->get_controls(),
				'title_field' => '{{{ chart_title }}}',
			]
		);
		$this->end_controls_section();// end: Section

	}

	/**
	 * Render Chart widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	*/
	protected function render() {
		$settings = $this->get_settings_for_display();

		$chart_group = !empty( $settings['chart_group'] ) ? $settings['chart_group'] : '';
		$chart_style = !empty( $settings['chart_style'] ) ? $settings['chart_style'] : 'doughnut';

		// Turn output buffer on
		ob_start();
		$chart_id = 'chart'.uniqid('-');
		?>
	  <canvas id="<?php echo esc_attr( $chart_id ); ?>"></canvas>
	    <script>
	    var ctx = document.getElementById('<?php echo esc_js( $chart_id ); ?>').getContext('2d');
	    var myChart = new Chart(ctx, {
	        type: '<?php echo esc_js( $chart_style ); ?>',
	        data: {
	            labels: [ <?php foreach( $chart_group as $chart_item ){ echo "'".$chart_item["chart_title"]."', "; } ?> ],
	            datasets: [{
	                label: '# of Votes',
	                data: [<?php foreach( $chart_group as $chart_item ){ echo $chart_item["chart_value"].", "; } ?>],
	                backgroundColor: [<?php foreach( $chart_group as $chart_item ){ echo "'".$chart_item["chart_color"]."', "; } ?>],
	                borderColor: [<?php foreach( $chart_group as $chart_item ){ echo "'".$chart_item["border_color"]."', "; } ?>],
                	borderWidth: 1
	            }]
	        },
	        options: {
	          responsive: true
	        }
	    });
	    </script>

		<?php // Return outbut buffer
		echo ob_get_clean();

	}
	/**
	 * Render Chart widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	*/

	//protected function _content_template(){}

}
Plugin::instance()->widgets_manager->register( new Elementor_Chart_Widget() );