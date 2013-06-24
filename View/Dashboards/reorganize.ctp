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
			,	draggable: {
					stop: function(event, ui){ 
						saveGridster( gridster.serialize() );
					}
				}
			,	serialize_params: function($w, wgd) {
					return {
							id: wgd.el[0].id
						,	col: wgd.col
						,	row: wgd.row
					};
				}
		}).data( 'gridster' );

		function saveGridster( oGridster ) {

			var oPostData = {};

			$.each( $( oGridster ), function( key, oWidget ) {

				oPostData[key] = {
						'id': oWidget.id
					,	'col': oWidget.col
					,	'row': oWidget.row
				};

			} );

			$.ajax({
					type: 'POST'
				,	data: oPostData
				,	url: '<?php echo str_ireplace( 'reorganize', 'ajaxSave', $this->here ); ?>'
				,	success: function( data ) {
					}
			});

		}

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
