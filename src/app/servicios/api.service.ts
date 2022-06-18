import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

export interface Nombres {
  nombres: string;
  apellidos: string;
  email: string;
  identificacion: string;
  coursename: string;
  notaFinal: string;
  fecha: string;
}
export interface Calificaciones extends Nombres {
  userid: string;
  fullname: string;
  shortname: string;
  courseid: string;
}
export interface Notificacion {
  nombre: string;
  correo: string;
  curso: string;
  asunto?: string;
  contenido?: string;
  altbody?: string;
}
export interface ErrorEnvio {
  userid: string;
  nombre: string;
  email: string;
  error: string;
}
@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private contHTML: string = `
  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
      <p>Estimado(a) Doctor(a) <strong>{{nombre}}</strong>:</p>
      <p>De parte del Grupo de Capacitación de ACG Calidad le queremos agradecer su participación en nuestro curso virtual <strong>{{curso}}</strong>.</p>
      <p>Ingrese a <a href="http://aulavirtual.acgcalidad.co/" target="_blank">nuestro Campus Virtual</a> (con el mismo usuario y contraseña de siempre) y haga clic sobre el vínculo <strong>OBTENER CERTIFICADO</strong> que se encuentra al inicio de la página principal.</p>
      <p>Si tiene alguna duda, puede consultar <a href="https://youtu.be/Gq-UkAvsFOo" target="_blank">nuestro instructivo en video</a>.</p>
      <p>Atentamente,</p>
      <br>
      <p>
        --
        <br>
        <strong>Grupo Capacitación ACG</strong>
      </p>
    </body>
  </html>
  `;
  private contAlt: string = `
  Estimado(a) Doctor(a) {{nombre}}:

  De parte del Grupo de Capacitación de ACG Calidad le queremos agradecer su participación en nuestro curso virtual {{curso}}.

  Ingrese a nuestro Campus Virtual con el mismo usuario y contraseña de siempre, http://aulavirtual.acgcalidad.co, y haga clic sobre el vínculo OBTENER CERTIFICADO que se encuentra al inicio de la página principal.

  Si tiene alguna duda, puede consultar nuestro instructivo en video https://youtu.be/Gq-UkAvsFOo.

  Atentamente,

  --
  Grupo Capacitación ACG
  `;
  private asunto: string = 'Certificado de curso virtual en ACG Calidad';
  constructor(private http: HttpClient) { }
  list(id: string): Observable<any> {
    return this.http.get(environment.ruta_api + 'assets/api/list.php?id=' + id, { responseType: 'json' });
  }
  getCert(id: string | null): Observable<any> {
    const get: string = id ? '?id=' + id : '';
    return this.http.get(environment.ruta_api + 'assets/api/getCert.php' + get, { responseType: 'json' });
  }
  listUsers(id: string | null = null): Observable<any> {
    const get: string = id ? '?id=' + id : '';
    return this.http.get(environment.ruta_api + 'assets/api/listUsers.php' + get, { responseType: 'json' });
  }
  sendMail(get: Notificacion): Observable<any> {
    // Envía notificaciones sobre GENERACIÓN DE CERTIFICADOS
    const data: FormData = new FormData();
    data.set('nombre', get.nombre);
    data.append('curso', get.curso ?? '');
    data.append('correo', get.correo);
    data.append('asunto', get.asunto ?? this.asunto);
    return this.http.post(environment.ruta_api + 'assets/api/sendMail.php', data, { responseType: 'json' });
  }
  postCert(data: any): Observable<any> {
    const postData: FormData = new FormData();
    Object.keys(data).forEach((k, i) => postData.append(k, String(Object.values(data)[i])));
    return this.http.post(environment.ruta_api + 'assets/api/postCert.php', postData, { responseType: 'json' });
  }
  postNot(id: string): Observable<any> {
    return this.http.get(environment.ruta_api + 'assets/api/postNot.php?id=' + id, { responseType: 'json' });
  }
  generaToken(): Observable<any> {
    return this.http.get(environment.ruta_api + 'assets/api/generaToken.php?accion', { responseType: 'json' });
  }
  notificaNuevos(data: any): Observable<any> {
    // Envía notificaciones sobre REGISTRO DE NUEVOS PARTICIPANTES EN PLATAFORMA
    const postData: FormData = new FormData();
    Object.keys(data).forEach((k, i) => postData.append(k, String(Object.values(data)[i])));
    return this.http.post(environment.ruta_api + 'assets/api/notificar.php', postData, { responseType: 'json' });
  }
}
