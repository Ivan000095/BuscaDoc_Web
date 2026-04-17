<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pacientes</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #00213D; padding-bottom: 10px; }
        .header h1 { color: #00213D; margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #00213D; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { border-bottom: 1px solid #ddd; padding: 8px; vertical-align: top; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .text-danger { color: #d32f2f; font-weight: bold; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>BuscaDoc - Expediente de Pacientes</h1>
        <p>Listado Administrativo de Pacientes Registrados | Sede: Ocosingo, Chiapas</p>
        <p style="font-size: 10px; margin-top: 5px;">
            Generado el: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Paciente / Correo</th>
                <th style="width: 80px; text-align: center;">Sangre</th>
                <th>Alergias Conocidas</th>
                <th>Padecimientos Previos</th>
                <th>Contacto Emergencia</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pacientes as $paciente)
                <tr>
                    <td>
                        <strong>{{ $paciente->user->name }}</strong><br>
                        <span style="color:gray; font-size: 10px;">{{ $paciente->user->email }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="text-danger">{{ $paciente->tipo_sangre ?? 'N/E' }}</span>
                    </td>
                    <td>{{ $paciente->alergias ?: 'Ninguna declarada' }}</td>
                    <td>{{ $paciente->padecimientos ?: 'Ninguno declarado' }}</td>
                    <td style="font-family: monospace;">{{ $paciente->contacto_emergencia ?: 'No registrado' }}</td>
                    <td>{{ $paciente->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No se encontraron pacientes con los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento confidencial generado automáticamente por el sistema BuscaDoc.
    </div>

</body>
</html>