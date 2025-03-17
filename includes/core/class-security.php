class Security {
    public static function sanitize_input($data) {
        return array_map('sanitize_text_field', $data);
    }
}

// Usage in form handling:
$clean_data = Security::sanitize_input($_POST);