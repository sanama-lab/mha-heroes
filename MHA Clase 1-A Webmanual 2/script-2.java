<script>
function mostrarInfo(id) {
  const info = document.getElementById(id);
  const visible = info.style.display === "block";
  
  // Oculta todas las demás
  document.querySelectorAll(".info").forEach(i => i.style.display = "none");
  
  // Muestra o cierra la seleccionada
  info.style.display = visible ? "none" : "block";
}
</script>
