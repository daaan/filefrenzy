<?php

class FileFrenzy extends FileFrenzyBase
{
    private FileFrenzyRequest $request;

    private array $allowedShorttagAttributes = [
        'path',
        'order',
        'filesizedecimals',
        'whitelistedextensions',
        'blacklistedextensions',
        'whitelistedfilenames',
        'blacklistedfilenames',
    ];

    public function __construct($atts)
    {
        parent::__construct($atts);

        $this->request = new FileFrenzyRequest($atts, $this->allowedShorttagAttributes);
    }

    /**
     *
     */
    public function display()
    {
        try {
            $path = $this->request->getPath();
        } catch (RuntimeException $e) {
            return $this->handleError($e);
        }

        try {
            $directoryListing = $this->getDirectoryListing($path);
        } catch (RuntimeException $e) {
            return $this->handleError($e);
        }

        $this->loadAssets();

        // Todo some form of templating
        return $this->directoryListingToHtml($directoryListing);
    }

    private function directoryListingToHtml($directoryListing)
    {
        // Generate unique Id for css class.
        // Don't use more-entropy because that generates a . which is illegal in css/js identifiers
        $uid = uniqid('filefrenzy-', false);

        // header
        $html = '<table id="' . $uid . '" class="display" style="width:100%">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="filefrenzy-list-filetype">' . __('Type', 'filefrenzy') . '</th>';
        $html .= '<th>' . __('File name', 'filefrenzy') . '</th>';
        $html .= '<th>' . __('Size', 'filefrenzy') . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';

        // body
        $html .= '<tbody>';

        $fileSizeDecimals = $this->request->getFileSizeDecimals();

        foreach ($directoryListing as $file) {
            if ($file['isdir']) {
                // Todo: make directories optional
                continue;
                $html .= '<tr>';
                $html .= '<td>dir</td>';
                $html .= '<td>' . $file['basename'] . '</td>';
                $html .= '<td></td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td><div class="filelisticon"><i class="fiv-cla fiv-icon-' . $file['extension'] . '"></i><p class="icon-desc">' . strtoupper($file['extension']) . '</p></div></td>';
                $html .= '<td><a href="' . $file['url'] . '">' . $file['filename'] . '</a></td>';
                $html .= '<td>' . $this->humanReadableFileSize($file['size'], $fileSizeDecimals) . '</td>';
                $html .= '</tr>';
            }
        }

        $dataTableOptions = [
            'paging: false',
            'columns: [{ name: \'filetype\' },{ name: \'filename\' },{ name: \'filesize\' }]',
        ];

        $order = $this->request->getOrder();
        if ($order && preg_match('/^[a-z0-9]*-\b(asc|desc)\b$/', $order)) {
            $orderExploded = explode('-', $order);

            $sortBy = 'idx';
            $sortValue = $orderExploded[0];
            if (!is_numeric($orderExploded[0])) {
                $sortBy = 'name';
                $sortValue = '\'' . $orderExploded[0] . '\'';
            }

            $dataTableOptions[] = 'order: {' . $sortBy . ': ' . $sortValue . ', dir: \'' . $orderExploded[1] . '\'}';
        }

        // end
        $html .= '</tfoot>';
        $html .= '</table>';
        $html .= '<script type="text/javascript">' . PHP_EOL;
        $html .= 'jQuery(document).ready(function() {' . PHP_EOL;
        $html .= 'new DataTable(\'#' . $uid . '\', {' . implode(',', $dataTableOptions) . '});' . PHP_EOL;
        $html .= '})' . PHP_EOL;
        $html .= '</script>';

        return $html;
    }

    private function loadAssets() {
        wp_enqueue_script( 'datatables.min.js', plugins_url( '/assets/js/datatables.min.js', FILEFRENZY_PLUGIN ), ['jquery']);
        wp_enqueue_style( 'datatables.min.css', plugins_url( '/assets/css/datatables.min.css', FILEFRENZY_PLUGIN ));
        wp_enqueue_style( 'FileFrenzy.css', plugins_url( '/assets/css/FileFrenzy.css', FILEFRENZY_PLUGIN ));
        wp_enqueue_style( 'file-icon-vectors.css', plugins_url( '/vendor/dmhendricks/file-icon-vectors/dist/file-icon-vectors.css', FILEFRENZY_PLUGIN ));
    }

    private function getDirectoryListing(string $path): array {
        $absoluteFullPath = $this->absolutePath . $path;
        if ( !file_exists( $absoluteFullPath ) || !is_dir( $absoluteFullPath) ) {
            throw new RuntimeException('Path \'' . $absoluteFullPath . '\' not accessible or not found');
        }

        $listing = scandir($absoluteFullPath); // Get complete listing
        $listing = array_diff($listing, array('.', '..')); // Remove dots from listing
        natcasesort($listing); // Properly sort listing

        $blacklistedExtensions = $this->request->getBlacklistedExtensions();
        $whitelistedExtensions = $this->request->getWhitelistedExtensions();
        $blacklistedFilenames = $this->request->getBlacklistedFilenames();
        $whitelistedFilenames = $this->request->getWhitelistedFilenames();

        if (!in_array('php', $whitelistedExtensions, true)) {
            $blacklistedExtensions[] = 'php';
        }

        $files = [];
        $directories = [];
        foreach ($listing as $file) {
            $fullFilePath = $absoluteFullPath . DIRECTORY_SEPARATOR . $file;

            // Todo: error handling on these functions
            $pathinfo = pathinfo($fullFilePath);
            $pathinfo['isdir'] = is_dir($fullFilePath);
            $pathinfo['url'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullFilePath);

            // Make sure to list directories first
            if ($pathinfo['isdir']) {
                $directories[] = $pathinfo;
            } else {
                if (!empty($whitelistedExtensions) && !in_array($pathinfo['extension'], $whitelistedExtensions, true)) {
                    continue;
                }
                if (!empty($whitelistedFilenames) && !in_array($file, $whitelistedFilenames, true)) {
                    continue;
                }
                if (
                    in_array($pathinfo['extension'], $blacklistedExtensions, true)
                    || in_array($file, $blacklistedFilenames, true)
                ) {
                    continue;
                }

                $pathinfo['size'] = filesize($fullFilePath);
                $files[] = $pathinfo;
            }
        }

        return array_merge($directories, $files);
    }

    private function handleError(RuntimeException $e) {
        return $e->getMessage();
    }

}
