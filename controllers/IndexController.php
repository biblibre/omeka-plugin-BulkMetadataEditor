<?php
/**
 * BulkMetadataEditor Index Controller class file
 *
 * This controller enforces selection rules on items and metadata
 * elements, and performs various builk editing operations
 * on the database of element texts associated with Omeka items.
 * This class contains the bulk of the functionality of the
 * BulkMetadataEditor plugin.
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The BulkMetadataEditor index controller class.
 *
 * @package BulkMetadataEditor
 */
class BulkMetadataEditor_IndexController extends Omeka_Controller_AbstractActionController
{

    protected $bulkEdit;

    public function init()
    {
        $this->_bulkEdit = $this->view->bulkEdit();
    }

    /**
     * Process form data and prepare to display the main BulkMetadataEditor form.
     *
     * If the form has been submitted, it performs the relevant changes.
     * Regardless, it populates the arrays of options for the view's
     * dropdown menus.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->form = new BulkMetadataEditor_Form_Main();

        // if the form was submitted
        if ($this->getRequest()->isPost()
            && $this->view->form->isValid($this->getRequest()->getPost())) {

            $params = $_REQUEST;
            try {
                $this->_bulkEdit->perform($params);
                $message = __('The requested changes have been applied to the database.');
                $status = 'success';
            } catch (Exception $e) {
                $message = $e->getMessage();
                $status = 'error';
            }

            $flashMessenger = $this->_helper->FlashMessenger->addMessage($message, $status);
        }
    }

    ///// AJAX ACTIONS /////

    /**
     * Retrieves selected items for preview.
     *
     * Retrieves the first few items matching the selection rules and sends them
     * to the view to be served to a browser preview script.
     *
     * @param void
     * @return void
     */
    public function itemsAction()
    {
        $params = $_REQUEST;
        $max = $this->_getParam('max');
        try {
            $items = $this->_bulkEdit->getItems($params, $max);
            $this->_helper->json($items);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * Retrieves number of selected items for preview.
     *
     * Retrieves the number of items matching the selection rules and sends them
     * to the view to be served to a browser preview script.
     *
     * @param void
     * @return void
     */
    public function countitemsAction()
    {
        $params = $_REQUEST;
        try {
            $count = count($this->_bulkEdit->getItems($params));
            $this->_helper->json($count);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * Retrieves the selected metadata elements for preview.
     *
     * Retrieves the number of items matching the selection rules and sends them
     * to the view to be served to a browser preview script.
     *
     * @param void
     * @return void
     */
    public function fieldsAction()
    {
        $params = $_REQUEST;
        $max = $this->_getParam('max');
        try {
            $items = $this->_bulkEdit->getItems($params, $max);
            $fields = $this->_bulkEdit->getFields($params, $items, $max);
            $this->_helper->json($fields);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * Retrieves the first few edits defined by the form input.
     *
     * Retrieves the first few changes defined by the form input and sends them
     * to the view to be served to a browser preview script.
     *
     * @param void
     * @return void
     */
    public function changesAction()
    {
        $params = $_REQUEST;
        $max = $this->_getParam('max');
        try {
            $changes = $this->_bulkEdit->getChanges($params, $max);
            $this->_helper->json($changes);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->_helper->json($e->getMessage());
        }
    }

    ///// END AJAX ACTIONS /////
}
