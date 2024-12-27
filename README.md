Langkah-langkah yang dibutuhkan untuk menjalankan web dengan baik :

- Install database server: MySql (menggunakan Xampp).
- Install software mysql management: phpMyAdmin dari Xampp.
- Install tomcat dan mondrian
- Extract ProjectUAS_DWO.zip ke dalam folder htdocs XAMPP (xampp/htdocs).
- Pindahkan file Projectuas_FactProduct.jsp, Projectuas_FactProduct.xml, Projectuas_Factsales.jsp, Projectuas_Factsales.xml ke dalam folder C:\xampp\tomcat\webapps\mondrian\WEB-INF\queries
- Jalankan Xampp control panel.
- Start apache, mysql server, dan tomcat.
- Buka phpMyAdmin (localhost/phpmyadmin).
- Buat database dengan nama projectuas_ 
- Import file projectuas_.sql ke dalam database projectuas_
- Jalankan aplikasi dengan mengakses http://localhost/ProjectUAS_DWO/
