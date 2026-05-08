<?php

namespace App\controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

/**
 * Handles document generation in DOCX format
 */
class DocumentGenerator {
    /**
     * Store FormHandler object
     * @var FormHandler
     */
    private $formHandler;

    /**
     * Constructor
     *
     * @param FormHandler $formHandler
     */
    public function __construct(FormHandler $formHandler) {
        $this->formHandler = $formHandler;
    }

    /**
     * Generate DOCX document
     *
     * @return string
     */
    public function generateDocument() {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText("User Information", ['bold' => true, 'size' => 18]);
        $section->addTextBreak(1);

        $section->addText("Full Name: " . $this->formHandler->getFullName());
        $section->addText("Phone Number: " . $this->formHandler->getPhoneNo());
        $section->addText("Email: " . $this->formHandler->getEmail());
        $section->addTextBreak(1);

        $section->addText("Marks Details", ['bold' => true]);

        $marks = $this->formHandler->getMarksTable();

        if (!empty($marks)) {
            $table = $section->addTable();

            $table->addRow();
            $table->addCell(4000)->addText("Subject");
            $table->addCell(2000)->addText("Marks");

            foreach ($marks as $mark) {
                $table->addRow();
                $table->addCell(4000)->addText($mark['subject']);
                $table->addCell(2000)->addText($mark['marks']);
            }
        } else {
            $section->addText("No marks given");
        }

        $section->addTextBreak(1);
        $imagePath = BASE_PATH . '/app/uploads/' . basename($this->formHandler->getImagePath());
        if (file_exists($imagePath)) {
            $section->addImage($imagePath, [
                'width' => 400,
                'height' => 400
            ]);
        }

        $docDir = BASE_PATH .  "/public/documents/";

        //If not exists then create the dir
        if (!is_dir($docDir)) {
            mkdir($docDir, 0777, true);
        }

        $fileName = "user_data_" . time() . ".docx";
        $filePath = $docDir . $fileName;

        //Write the data into server
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Downlaod DOCX document
     *
     * @param string $docPath Path of the docx document
     */
    public function downloadDocx($docPath) {
        if (file_exists($docPath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . basename($docPath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: public');
            header('Expires: 0');
            header('Content-Length: ' . filesize($docPath));

            readfile($docPath);
            exit;
        }
    }
}

/**
 * Handles email syntax and validity verification
 */
class EmailValidator {
    /**
     * Store email
     * @var string
     */
    private $email;

    /**
     * Constructor
     *
     * @param string $email
     */
    public function __construct($email) {
        $this->email = trim($email);
    }

    /**
     * Check email syntax
     *
     * @return bool
     */
    public function validateSyntax() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check valid email using mailboxlayer API
     *
     * @return bool
     */
    public function validateEmailExistence() {
        if (empty($_ENV["MAILBOX_LAYER_API_KEY"])) {
            return true;
        }

        $accessKey = $_ENV["MAILBOX_LAYER_API_KEY"];

        $url = "http://apilayer.net/api/check?access_key={$accessKey}&email={$this->email}&smtp=1&format=1";

        $context = stream_context_create([
            'http' => [
                'timeout' => 5
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if (!$response) {
            return false;
        }

        $result = json_decode($response, true);

        return !empty($result['format_valid']) && !empty($result['smtp_check']);
    }
}

/**
 * Handles form data validation, image upload,
 * full name generation, and marks table parsing.
 */
class FormHandler {

    /**
     * Store first name
     * @var string
     */
    private $firstName;

    /**
     * Store last name
     * @var string
     */
    private $lastName;

    /**
     * Store phone number
     * @var string
     */
    private $phone;

    /**
     * Store email
     * @var string
     */
    private $email;

    /**
     * Store uploaded image file data
     * @var array|null
     */
    private $image;

    /**
     * Store subject marks textarea input
     * @var string
     */
    private $marks;

    /**
     * Store generated full name
     * @var string
     */
    private $fullName;

    /**
     * Store uploaded image path
     * @var string
     */
    private $imagePath;

    /**
     * Store parsed subject marks
     * @var array
     */
    private $marksTable = [];

    /**
     * Store validation errors
     * @var array
     */
    private $errors = [];

    /**
     * Constructor to initialize form data
     *
     * @param array $postData
     * @param array $filesData
     */
    public function __construct($postData, $filesData) {
        $this->firstName = trim($postData['first_name'] ?? "");
        $this->lastName = trim($postData['last_name'] ?? "");
        $this->phone = trim($postData['phone'] ?? "");
        $this->email = trim($postData['email'] ?? "");
        $this->marks = trim($postData['marks'] ?? "");
        $this->image = $filesData['image'] ?? null;
    }

    /**
     * Generic validation rule method
     *
     * @param string $fieldName
     * @param bool $condition
     * @param string $errorMessage
     * @param bool &$valid
     *
     * @return bool
     */
    public function validateRule($fieldName, $condition, $errorMessage, &$valid) {
        if (!$condition) {
            $this->errors[$fieldName] = $errorMessage;
            $valid = false;
            return false;
        }
        return true;
    }

    /**
     * Validate all form fields
     *
     * @return bool
     */
    public function validate() {
        $valid = true;

        // Validate first name
        $this->validateRule(
            'first_name',
            $this->firstName !== "" && ctype_alpha($this->firstName),
            "Only alphabets allowed.",
            $valid
        );

        // Validate last name
        $this->validateRule(
            'last_name',
            $this->lastName !== "" && ctype_alpha($this->lastName),
            "Only alphabets allowed.",
            $valid
        );

        // Validate Indian phone number
        $this->validateRule(
            'phone',
            preg_match('/^(?:\+91|91)?[6-9]\d{9}$/', $this->phone),
            "Phone must start with +91 and 10 digits.",
            $valid
        );

        // Validate email syntax
        $emailValidator = new EmailValidator($this->email);

        $this->validateRule(
            'email',
            $emailValidator->validateSyntax(),
            "Invalid email syntax.",
            $valid
        );

        // Validate real email existence
        if ($valid) {
            $this->validateRule(
                'email',
                $emailValidator->validateEmailExistence(),
                "Email does not exist.",
                $valid
            );
        }

        // Validate image upload
        if ($this->validateRule(
            'image',
            $this->image && $this->image['error'] === 0,
            "Image upload failed.",
            $valid
        )) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = mime_content_type($this->image['tmp_name']);

            $this->validateRule(
                'image',
                in_array($fileType, $allowedTypes),
                "Only JPG, JPEG, PNG allowed.",
                $valid
            );
        }

        return $valid;
    }

    /**
     * Parse subject marks from textarea
     *
     * Format:
     * English|80
     * Math|90
     *
     * @return bool
     */
    public function parseMarks() {
        $lines = explode("\n", $this->marks);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $parts = explode("|", $line);

            // Validate line format
            if (count($parts) != 2) {
                $this->errors['marks'] = "Use format Subject|Marks";
                return false;
            }

            $subject = trim($parts[0]);
            $marks = trim($parts[1]);

            // Check numeric marks
            if (!is_numeric($marks)) {
                $this->errors['marks'] = "Marks must be numeric.";
                return false;
            }

            // Store parsed values
            $this->marksTable[] = [
                'subject' => $subject,
                'marks' => $marks
            ];
        }

        return true;
    }

    /**
     * Upload image to uploads folder
     *
     * @return void
     */
    public function uploadImage() {
        $uploadDir = BASE_PATH . "/public/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($this->image['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($this->image['tmp_name'], $targetPath)) {
            $this->imagePath = "/uploads/{$fileName}";
        } else {
            $this->errors['image'] = "Image upload failed.";
        }
    }

    /**
     * Generate full name
     *
     * @return void
     */
    public function generateFullName() {
        $this->fullName = $this->firstName . " " . $this->lastName;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * Get Phone No
     *
     * @return string
     */
    public function getPhoneNo() {
        return $this->phone;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Get uploaded image path
     *
     * @return string
     */
    public function getImagePath() {
        return $this->imagePath;
    }

    /**
     * Get subject marks table
     *
     * @return array
     */
    public function getMarksTable() {
        return $this->marksTable;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
}

class FormController {

    /**
     * Constructor
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check logged in user
     *
     * @return void
     */
    private function authCheck() {
        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    /**
     * Show form
     *
     * @return void
     */
    public function index() {
        $this->authCheck();

        $errors = [];

        require_once BASE_PATH . "/app/views/form.php";
    }

    /**
     * Process submitted form
     *
     * @return void
     */
    public function generatePDF() {
        $this->authCheck();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /form");
            exit;
        }

        $form = new FormHandler($_POST, $_FILES);

        if ($form->validate() && $form->parseMarks()) {
            $form->generateFullName();
            $form->uploadImage();

            if (!empty($form->getErrors())) {
                $errors = $form->getErrors();
                require_once BASE_PATH . "/app/views/form.php";
                return;
            }

            $docGenerator = new DocumentGenerator($form);
            $docPath = $docGenerator->generateDocument();
            $docGenerator->downloadDocx($docPath);
        } else {
            $errors = $form->getErrors();
            require_once BASE_PATH . "/app/views/form.php";
        }
    }
}
