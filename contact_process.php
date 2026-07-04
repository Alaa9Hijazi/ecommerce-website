<?php
include_once 'config.php';

//التأكد من إرسال البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //  استقبال البيانات من النموذج
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    //  استخدام الاستعلامات
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    
    // ربط المتغيرات بالاستعلام  (الحرف 's' يعني أن البيانات نصية String)
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    //  تنفيذ الاستعلام والتحقق من نجاح العملية
    if ($stmt->execute()) {

        // تحويل + تمرير حالة نجاح للـ JS
        header("Location: contact.php?sent=1");
        exit();

    } else {
        // في حال حدوث خطأ في قاعدة البيانات
        echo "Error sending message: " . $stmt->error;
    }
    
    //  إغلاق الاستعلام بعد الانتهاء
    $stmt->close();

} else {
    // منع الوصول المباشر لهذا الملف
    header("Location: contact.php");
    exit();
}
?>