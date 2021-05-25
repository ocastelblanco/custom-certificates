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

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  constructor(private http: HttpClient) { }
  list(id: string): Observable<any> {
    return this.http.get(environment.ruta_api + 'assets/api/list.php?id=' + id, { responseType: 'json' });
  }
  getCert(id: string | null): Observable<any> {
    const get: string = id ? '?id=' + id : '';
    return this.http.get(environment.ruta_api + 'assets/api/getCert.php' + get, { responseType: 'json' });
  }
  listUsers(get: any[] = []): Observable<any> {
    const getVar: string = get.length > 0 ? '?' + get.map(e => Object.keys(e) + '=' + Object.values(e)).join('&') : '';
    return this.http.get(environment.ruta_api + 'assets/api/listUsers.php', { responseType: 'json' });
  }
}
