import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

export interface User {
  id: string;
  idnumber: string;
  firstname: string;
  lastname: string;
  email: string;
  city: string;
  country: string;
  institution: string;
  admin: boolean;
  sesionid?: string;
}

@Injectable({
  providedIn: 'root'
})
export class SesionService {
  private sesionActiva: BehaviorSubject<User | null> = new BehaviorSubject<User | null>(null);
  public perfil!: User | null;
  constructor(private http: HttpClient) { }
  sesion(): BehaviorSubject<User | null> {
    this.http.get(environment.ruta_api + 'assets/api/sesion.php', { responseType: 'json' }).subscribe(s =>
      s ? this.fillPerfil(s) : this.sesionActiva.next(null)
    );
    return this.sesionActiva;
  }
  login(user: string, pass: string): Observable<any> {
    const data: FormData = new FormData();
    data.set('user', user);
    data.append('pass', pass);
    const salida: Observable<any> = this.http.post(environment.ruta_login, data, { responseType: 'json' });
    salida.subscribe(p => this.listAdmin().subscribe(a => (p && a) ? this.fillPerfil(p, a) : null));
    return salida;
  }
  logout() {
    this.http.get(environment.ruta_api + 'assets/api/sesion.php?logout=1', { responseType: 'json' }).subscribe(s =>
      !s ? this.sesionActiva.next(null) : null
    )
  }
  private listAdmin(): Observable<any> {
    return this.http.get(environment.ruta_api + 'assets/api/listAdmin.php', { responseType: 'json' });
  }
  private fillPerfil(p: any, a: string[] | null = null): void {
    this.perfil = {
      id: p.id,
      idnumber: p.idnumber,
      firstname: p.firstname,
      lastname: p.lastname,
      email: p.email,
      city: p.city,
      country: p.country,
      institution: p.institution,
      admin: a ? a.includes(p.id) : p.admin
    }
    const fd: FormData = new FormData();
    Object.entries(this.perfil).forEach(l => fd.append(l[0], l[1]));
    this.http.post(environment.ruta_api + 'assets/api/sesion.php', fd, { responseType: 'json' }).subscribe(r => {
      this.perfil = r ? Object(r) : null;
      this.sesionActiva.next(this.perfil);
    });
  }
}
