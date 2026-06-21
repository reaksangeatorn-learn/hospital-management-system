</div>
<footer style="margin-left:240px;text-align:center;padding:10px;font-size:.75rem;color:#adb5bd;border-top:1px solid #ececec;background:#fff;">
    &copy; <?= date('Y') ?> MediCare Clinic
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function tick(){const n=new Date(),e=document.getElementById('clock');if(e)e.textContent=n.toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'})+' '+n.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});}
tick();setInterval(tick,1000);
const fl=document.querySelector('.alert');if(fl)setTimeout(()=>fl.style.display='none',4000);
</script>
</body>
</html>