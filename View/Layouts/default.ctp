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
					'/autotask/js/jquery-1.9.1.min'
				,	'/autotask/js/bootstrap.min'
			);

			echo $this->Html->script( $aScripts );
		?>
	</head>

	<body <?php
		if( !$bIsMobile ) {
			echo 'style="padding: 60px 0 0 0;"';
		}
	?>>

		<?php echo $this->element( 'menu' ); ?>

		<div class="container-fluid" style="padding-left: 0;">

			<div class="row-fluid">
				<?php
					echo $this->Session->flash( 'flash', array(
							'element' => 'flash_success'
					) );
				?>
			</div>

			<?php echo $this->fetch( 'content' ); ?>
		</div>

	</body>
</html>