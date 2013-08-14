<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
			$aStylesheets = array(
					'/autotask/css/bootstrap.min'
				,	'/autotask/css/bootstrap-responsive.min'
				,	'/autotask/css/font-awesome.min'
				,	'/autotask/css/customisations'
			);

			if( $bIsMobile ) {
				$aStylesheets[] = '/autotask/css/mobile';
			}

			echo $this->Html->css( $aStylesheets );

			$aScripts = array(
					'/autotask/js/jquery-1.10.2.min'
				,	'/autotask/js/bootstrap.min'
				,	'/autotask/js/jquery.jeditable.mini'
				,	'/autotask/js/fullscreen'
			);

			echo $this->Html->script( $aScripts );

			if(
				'Dashboards' == $this->name
				&&
				'display' == $this->action
			) {

				$iIsDashboardFullscreen = $this->Session->read( 'Dashboard.' . $this->request->data['Dashboard']['id'] . '.fullscreen' );

				if( 1 == $iIsDashboardFullscreen ) {
					echo '<script>$(function() {$( ".navbar" ).hide();});</script>';
				}

			}

		?>
	</head>

	<body <?php
		if( !$bIsMobile ) {

			if(
				'Dashboards' == $this->name
				&&
				'display' == $this->action
			) {
				if( 0 == $iIsDashboardFullscreen ) {
					echo 'style="padding: 60px 0 0 0;"';
				} else {
					echo 'style="padding: 0;"';
				}

			} else {
				echo 'style="padding: 60px 0 0 0;"';
			}
		}
	?>>

		<?php
			if(
				'Dashboards' == $this->name
				&&
				'display' == $this->action
			) {
		?>
			<div class="fullscreen" rel="<?php echo $this->request->data['Dashboard']['id']; ?>"><i class="icon-fullscreen icon-white" title="Fullscreen"></i></div>
		<?php }

			echo $this->element( 'menu' );
		?>

		<div class="container-fluid" style="padding-left: 0;">

			<div class="row-fluid">
				<?php
					if( 'flash_error' == $this->Session->read( 'Message.flash.element' ) ) {

						echo $this->Session->flash( 'flash', array(
								'element' => 'flash_error'
						) );

					} else {

						echo $this->Session->flash( 'flash', array(
								'element' => 'flash_success'
						) );

					}

				?>
			</div>

			<?php echo $this->fetch( 'content' ); ?>
		</div>

	</body>
</html>