<?php
class Api_Api_Statistic extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';
	
	public function updateViewCount( $params ) {
		if( empty( $params[ 'project_id' ] ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'Missing project_id' )
			);
		}
		
		$project = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
			
		$row = $project->fetchRow( 'project_id ='. $params[ 'project_id' ] );
		$updated = $row->incrementViewCount();
		
		return array(
			'data'  => array(
				'project_id'    => $updated->project_id,
				'project_views' => $updated->project_views
			),
			'error' => array()
		);
	}
}