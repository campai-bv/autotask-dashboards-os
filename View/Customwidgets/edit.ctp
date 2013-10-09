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
	<li class="active">Custom Widgets</li>
</ul>

<?php

	echo '<h2>Custom widgets</h2> Custom widget view files located at /View/Elements/Widgets/CustomWidgets';
	
?>
	<table class="table control-label" style="width:100%;">
		<thead>
			<tr>
				<th class="control-label">ID</th>
				<th class="control-label">Name</th>
				<th>Size</th>
				<th class="control-label"></th>
				<th class="control-label"></th>
			</tr>
		</thead>
		<tbody>
		
		<?php foreach( $aWidgets as $aWidget) { ?>
		
			<tr class="row_2">
				<td class="control-label"><?php echo $aWidget['Customwidget']['id']; ?></td>
				<td class="control-label"><?php echo $aWidget['Customwidget']['default_name']; ?></td>
				<td class="control-label"><?php echo $aWidget['Customwidget']['data_sizex'] ." x ". $aWidget['Customwidget']['data_sizey']; ?></td>
				<td class="control-label">
					<?php
						echo $this->Html->link( 'Edit', array(
								'plugin' => 'autotask'
							,	'controller' => 'customwidgets'
							,	'action' => 'edit'
							,	$aWidget['Customwidget']['id']
						) );
					?>
				</td>
				<td class="control-label">
					<?php
						echo $this->Html->link( 'Delete', array(
								'plugin' => 'autotask'
							,	'controller' => 'customwidgets'
							,	'action' => 'delete'
							,	$aWidget['Customwidget']['id']
						) );
					?>
				</td>
			</tr>	
		
		<?php } ?>
		
		</tbody>
	</table>
		
	<?php

	echo $this->Form->create( 'Autotask.Customwidget', array(
			'class' => 'form-horizontal'
		,	'inputDefaults' => array(
					'div' => array(
							'class' => 'control-group input'
					)
				,	'label' => array(
							'class' => 'control-label'
					)
				,	'between' => '<div class="controls">'
				,	'after' => '</div>'
				,	'class' => 'span2'
			)
	) );

	$id = '';
	$name = '';
	$xaxis = 1;
	$yaxis = 1;
	$title = '<h2>New custom widget</h2>';
	
	if(isset($aEdit)){
		$id = $aEdit['Customwidget']['id'];
		$name = $aEdit['Customwidget']['default_name'];
		$xaxis = $aEdit['Customwidget']['data_sizex'];
		$yaxis = $aEdit['Customwidget']['data_sizey'];
		$title = '<h2>Edit custom widget #'.$id.'</h2>';
	}
	
	echo $title;
	
	echo $this->Form->hidden( 'Customwidget.id', array(
			'value' => $id
	) );

	echo $this->Form->input( 'Customwidget.default_name', array(
			'class' => 'span4'
		,	'type' => 'text'
		,	'default' => $name
		,	'label' => array(
					'text' => 'Name'
				,	'class' => 'control-label'
			)
	) );
	
	echo $this->Form->input( 'Customwidget.data_sizex', array(
			'type' => 'select'
		,	'options' => array( 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6 )
		,	'class' => 'select'
		,	'default' => $xaxis
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Widget width'
			)
	) );
	
	echo $this->Form->input( 'Customwidget.data_sizey', array(
			'type' => 'select'
		,	'options' => array( 1=>1, 2=>2, 3=>3, 4=>4 )
		,	'class' => 'select'
		,	'default' => $yaxis
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Widget hight'
			)
	) );

	echo $this->Form->submit( 'Save', array(
			'class' => 'btn btn-success'
	) );

	echo $this->Form->end();