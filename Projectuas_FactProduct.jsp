<%@ page session="true" contentType="text/html; charset=ISO-8859-1" %> <%@
taglib uri="http://www.tonbeller.com/jpivot" prefix="jp" %> <%@ taglib
prefix="c" uri="http://java.sun.com/jstl/core" %>

<jp:mondrianQuery
  id="query01"
  jdbcDriver="com.mysql.jdbc.Driver"
  jdbcUrl="jdbc:mysql://localhost/projectuas_?user=root&password="
  catalogUri="/WEB-INF/queries/Projectuas_FactProduct.xml">

  SELECT {[Measures].[Planned Cost], [Measures].[Actual Cost]} ON COLUMNS,
         {([Time].[All Times], [Product].[All Products], [ScrapReason].[All ScrapReason])} ON ROWS 
  FROM [Product]
</jp:mondrianQuery>

<c:set var="title01" scope="session">Query Project UAS DWO using Mondrian OLAP</c:set>
