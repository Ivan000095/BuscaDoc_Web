<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Farmacias</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #00213D; padding-bottom: 10px; }
        .header h1 { color: #00213D; margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #00213D; color: white; padding: 10px; text-align: left; font-size: 12px; }
        td { border-bottom: 1px solid #ddd; padding: 8px; vertical-align: top; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>BuscaDoc - Directorio Médico</h1>
        <p>Reporte de Farmacias Registradas | Sede: Ocosingo, Chiapas</p>
        <p style="font-size: 11px; margin-top: 5px;">
            Generado el: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre de la Farmacia</th>
                <th>Propietario / Contacto</th>
                <th>RFC</th>
                <th>Teléfono</th>
                <th>Horario de Atención</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($farmacias as $farmacia)
                <tr>
                    <td><strong>{{ $farmacia->nom_farmacia }}</strong></td>
                    <td>
                        {{ $farmacia->user->name }}<br>
                        <span style="color:gray; font-size: 10px;">{{ $farmacia->user->email }}</span>
                    </td>
                    <td style="font-family: monospace;">{{ $farmacia->rfc }}</td>
                    <td>{{ $farmacia->telefono }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($farmacia->horario_entrada)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($farmacia->horario_salida)->format('H:i') }}
                    </td>
                    <td>{{ $farmacia->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No se encontraron farmacias con los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el sistema BuscaDoc.
    </div>

</body>
</html>