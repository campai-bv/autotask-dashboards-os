<?php echo $this->Html->script( '/autotask/js/highcharts' ); ?>

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