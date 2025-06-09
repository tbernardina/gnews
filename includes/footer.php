    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> GNEWS - Portal de Notícias Acessível. Todos os direitos reservados.</p>
                <a href="#top" class="back-to-top" aria-label="Voltar ao topo da página">Voltar ao Topo &uarr;</a>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script> 
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close(); // Fecha a conexão com o BD se estiver aberta
}
?>

