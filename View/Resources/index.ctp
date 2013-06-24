<ul class="breadcrumb">
	<li>
		<?php
			echo $this->Html->link( '<i class="icon-home"></i>', array(
					'plugin' => 'autotask'
				,	'controller' => 'dashboards'
				,	'action' => 'index'
			), array(
					'escape' => false
			) );
		?> <span class="divider">/</span>
	</li>
	<li class="active">Resources</li>
</ul>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if( !empty( $aResources) ) {

				foreach ( $aResources as $iKey => $aResource ) {

					echo '<tr>';

						echo '<td>';
							echo $aResource['Resource']['id'];
						echo '</td>';

						echo '<td>';

							$sName = '<i>Unspecified ticket status name</i>';

							if( !empty( $aResource['Resource']['name'] ) ) {
								$sName = $aResource['Resource']['name'];
							}

							echo $this->Html->link( $sName, array(
									'plugin' => 'autotask'
								,	'controller' => 'resources'
								,	'action' => 'edit'
								,	$aResource['Resource']['id']
							), array(
									'escape' => false
							) );

						echo '</td>';

					echo '</tr>';

				}

			}

		?>
	</tbody>
</table>