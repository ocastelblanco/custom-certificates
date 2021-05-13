import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

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
  listUsers(): Observable<any> {
    return this.http.get(environment.ruta_api + 'assets/api/listUsers.php', { responseType: 'json' });
  }
}
