/**
 * Hijazi Store - Main JavaScript File
 * وظيفة الملف: إدارة التنبيهات، تنظيف الروابط، وتأكيد عمليات الحذف
 */

document.addEventListener("DOMContentLoaded", function () {

    // استخراج البارامترات من رابط الصفحة الحالي
    const params = new URLSearchParams(window.location.search);

    // ============================================================
    // أولاً: نظام التنبيهات التفاعلية
    // ============================================================

    // تنبيه إضافة منتج
    if (params.get("added") === "1") {
        alert("Product added successfully! ✅");
        window.history.replaceState({}, document.title, "admin_products.php");
    }

    // تنبيه إضافة منتج للسلة
    if (params.get("cart") === "1") {
        alert("Product added to cart 🛒");
        window.history.replaceState({}, document.title, "cart.php");
    }

    // تنبيه تسجيل الدخول
    if (params.get("login_required") === "1") {
        alert("Please log in first to continue");
        window.history.replaceState({}, document.title, "login.php");
    }

    // تنبيه تحديث حالة الطلب
    if (params.get("updated") === "1") {
        alert("Order status updated successfully");
        window.history.replaceState({}, document.title, "admin_orders.php");
    }

    // تنبيه السلة الفارغة
    if (params.get("empty") === "1") {
        alert("Your cart is empty!");
        window.location.href = "products.php";
    }

    // تنبيه إرسال رسالة تواصل
    if (params.get("sent") === "1") {
        alert("Your message has been sent successfully! Thank you for contacting us.");
        window.history.replaceState({}, document.title, "contact.php");
    }

    // تنبيه إنشاء حساب جديد
    if (params.get("register") === "success") {
        alert("Account created successfully! You can now log in.");
        window.location.href = "login.php";
    }

    // تنبيه حذف منتج
    if (params.get("deleted") === "1") {
        alert("Product deleted successfully! ✅");
        window.history.replaceState({}, document.title, "admin_products.php");
    }

    // تنبيه تسجيل الخروج
    if (params.get("logout") === "1") {
        alert("Logged out successfully 👋");
        window.history.replaceState({}, document.title, "index.php");
    }

    // ============================================================
    // ثانياً: تأكيدات عمليات الحذف
    // ============================================================

    // تأكيد حذف رسائل التواصل
    document.querySelectorAll(".delete-message-link").forEach(link => {
        link.addEventListener("click", e => {
            if (!confirm("Are you sure you want to delete this message?")) {
                e.preventDefault();
            }
        });
    });

    // تأكيد حذف المنتجات
    document.querySelectorAll(".delete-product").forEach(link => {
        link.addEventListener("click", e => {
            if (!confirm("Are you sure you want to permanently delete this product?")) {
                e.preventDefault();
            }
        });
    });

    // تأكيد حذف المستخدمين
    document.querySelectorAll(".delete-user").forEach(link => {
        link.addEventListener("click", e => {
            if (!confirm("Are you sure you want to permanently delete this user account?")) {
                e.preventDefault();
            }
        });
    });

});