INSERT INTO permiso (idrol, modulo, acceso)
SELECT r.idrol, m.modulo,
       CASE WHEN r.nombre = 'Administrador' THEN 1 ELSE 0 END
FROM rol r
CROSS JOIN (
    SELECT 'inicio' AS modulo
    UNION ALL SELECT 'productos'
    UNION ALL SELECT 'proveedores'
    UNION ALL SELECT 'ingresos'
    UNION ALL SELECT 'ventas'
    UNION ALL SELECT 'inventario'
    UNION ALL SELECT 'reportes'
    UNION ALL SELECT 'usuarios'
    UNION ALL SELECT 'permisos'
) m;

