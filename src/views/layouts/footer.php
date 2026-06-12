<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Vent Then Validate. All rights reserved.</p>
</footer>
<script>
    function togglePreview(id) {
        var row = document.getElementById(id);
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
</script>
</body>

</html>