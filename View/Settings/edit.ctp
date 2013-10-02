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
	<li class="active">Settings</li>
</ul>

<?php
	echo $this->Form->create( 'Autotask.Setting', array(
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

	echo $this->Form->input( 'Setting.id' );

	echo $this->Form->input( 'Setting.app_title', array(
			'class' => 'span3'
	) );

	echo '<h2>Access restriction</h2>';

	echo $this->Form->input( 'Setting.ips', array(
			'class' => 'span4'
		,	'type' => 'textarea'
		,	'label' => array(
					'text' => 'IPs with access'
				,	'class' => 'control-label'
			)
		,	'placeholder' => '155.12.0.55 # Office (location Amsterdam)'
	) );
	
	echo '<h2>Widget y-axis length</h2>';
	echo '<h5>Set to 0 for system chosen length (lists all entries)</h5>';
	
	echo $this->Form->input( 'Setting.Accounts.Widget.data_sizey', array(
			'type' => 'select'
		,	'options' => array( 0, 1, 2, 3, 4)
		,	'class' => 'select'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Accounts'
			)
	) );
	
	echo $this->Form->input( 'Setting.Queues.Widget.data_sizey', array(
			'type' => 'select'
		,	'options' => array( 0, 1, 2, 3, 4)
		,	'class' => 'select'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Queues'
			)
	) );
	
	echo $this->Form->input( 'Setting.Resources.Widget.data_sizey', array(
			'type' => 'select'
		,	'options' => array( 0, 1, 2, 3, 4)
		,	'class' => 'select'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Resources'
			)
	) );
//

	echo $this->Form->submit( 'Save', array(
			'class' => 'btn btn-success'
	) );

	echo $this->Form->end();