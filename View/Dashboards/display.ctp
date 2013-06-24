<?php
	echo $this->Html->script( '/autotask/js/highcharts' );
	echo $this->Html->script( '/autotask/js/jquery.gridster.min' );
	echo $this->Html->script( '/autotask/js/gridster-init.js' );
	echo $this->Html->css( '/autotask/css/jquery.gridster.min' );
?>

<script>
	$(function() {

		var gridster = $(".gridster ul").gridster({
				widget_margins: [iMarginHorizontal, iMarginVertical]
			,	widget_base_dimensions: [iUnitWidth, iUnitHeight]
		}).data( 'gridster' ).disable();

	});
</script>

<div class="gridster">
	<ul>
		<?php
			foreach ( $aWidgets as $aWidget ) {
				echo $this->Autotask->widget( $aWidget );
			}
		?>
	</ul>
</div>

<script type="text/javascript">

	window.onload = setupRefresh;

	function setupRefresh() {
		setTimeout("refreshPage();", 60000); // milliseconds
	}

	function refreshPage() {
		window.location = location.href;
	}

</script>