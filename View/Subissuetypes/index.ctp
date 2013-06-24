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
	<li class="active">Subissuetypes</li>
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
			if( !empty( $aSubissuetypes ) ) {

				foreach ( $aSubissuetypes as $iKey => $aSubissuetype ) {

					echo '<tr>';

						echo '<td>';
							echo $aSubissuetype['Subissuetype']['id'];
						echo '</td>';

						echo '<td>';

							$sName = '<i>Unspecified sub issue type name</i>';

							if( !empty( $aSubissuetype['Subissuetype']['name'] ) ) {
								$sName = $aSubissuetype['Subissuetype']['name'];
							}

							echo $this->Html->link( $sName, array(
									'plugin' => 'autotask'
								,	'controller' => 'subissuetypes'
								,	'action' => 'edit'
								,	$aSubissuetype['Subissuetype']['id']
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