<?php

namespace Kanboard\Action;

use Kanboard\Model\TaskModel;

/**
 * Move a task to another column when an assignee is set
 *
 * @package action
 * @author  Francois Ferrand
 */
class TaskMoveColumnAssigned extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Move the task to another column when assigned to a user');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            TaskModel::EVENT_ASSIGNEE_CHANGE,
            TaskModel::EVENT_UPDATE,
        );
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
            'src_column_id' => t('Source column'),
            'dest_column_id' => t('Destination column')
        );
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array(
            'task_id',
            'column_id',
            'owner_id'
        );
    }

    /**
     * Execute the action (move the task to another column)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $original_task = $this->taskFinderModel->getById($data['task_id']);

        return $this->taskPositionModel->movePosition(
            $data['project_id'],
            $data['task_id'],
            $this->getParam('dest_column_id'),
            $original_task['position'],
            $original_task['swimlane_id'],
            false
        );
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        return $data['column_id'] == $this->getParam('src_column_id') && $data['owner_id'] > 0;
    }
}
