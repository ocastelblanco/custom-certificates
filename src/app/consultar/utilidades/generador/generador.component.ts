import { Component, OnInit } from '@angular/core';
import { ApiService, Curso, Pais, Participante } from 'src/app/servicios/api.service';
import { ExcelService } from 'src/app/servicios/excel.service';

@Component({
  selector: 'app-generador',
  templateUrl: './generador.component.html',
  styleUrls: ['./generador.component.scss']
})
export class GeneradorComponent implements OnInit {
  data: Participante[] = [];
  convirtiendo: boolean = false;
  archivoCargado: boolean = false;
  partSel: Participante[] = [];
  listaCursos: { corto: string, comun: string; }[] = [];
  listaPaises: Pais[] = this.api.paises;
  campos: string[] = [
    "firstname",
    "lastname",
    "username",
    "password",
    "email",
    "institution",
    "city",
    "country",
    "course1",
    "enroltimestart1",
    "enrolperiod1",
    "course2",
    "enroltimestart2",
    "enrolperiod2",
    "course3",
    "enroltimestart3",
    "enrolperiod3",
    "cohort1",
    "idnumber",
  ];
  campoNoEdit: number[] = [2, 3];
  campoArea: number[] = [0, 1, 5, 6, 18];
  campoEmail: number[] = [4];
  campoFecha: number[] = [9, 12, 15, 17];
  campoNum: number[] = [10, 13, 16];
  campoCurso: number[] = [8, 11, 14];
  campoPais: number[] = [7];
  campoOrigen: number[] = [4, 18];
  constructor(private api: ApiService, private excel: ExcelService) { }
  ngOnInit(): void {
    this.listaCursos = [];
    this.api.cursos.forEach((c: Curso) => this.listaCursos.push({ corto: c.corto, comun: c.comun }));
  }
  sueltaArchivo(archivo: File): void {
    this.archivoCargado = true;
    this.convirtiendo = true;
    this.procesaExcel(archivo);
  }
  procesaExcel(archivo: File): void {
    this.excel.readExcelFile(archivo).subscribe(res => {
      if (res) {
        const data: Participante[] = [];
        res.forEach((row: string[], index: number) => {
          if (index > 0 && row.length > 0 && row[0]) {
            data.push({
              firstname: this.nomPropio(row[0]),
              lastname: this.nomPropio(row[1]),
              username: this.limpiaID(row[9]),
              password: this.creaPassword(row[4].toLowerCase()),
              email: row[4].toLowerCase(),
              institution: this.nomInstitucion(row[5]),
              city: this.nomPropio(row[6]),
              country: 'CO',
              course1: this.getCurso(row[8]).corto,
              enroltimestart1: this.generaFecha(),
              enrolperiod1: this.getCurso(row[8]).plazo,
              cohort1: this.generaFecha(),
              idnumber: this.limpiaID(row[9]),
            });
          }
        });
        this.data = this.unificaCursos(data);
        this.convirtiendo = false;
      }
    });
  }
  nomPropio(nombre: string): string {
    return nombre.trim().replace(/[^\s]\S*/g, function (t) { return t.charAt(0).toUpperCase() + t.substring(1).toLowerCase(); });
  }
  limpiaID(id: string | number): string {
    return String(id).replace(/[^a-zA-Z0-9]/g, '');
  }
  nomInstitucion(nombre: string): string {
    return nombre.trim().indexOf(' ') > -1 ? this.nomPropio(nombre).trim() : nombre.trim();
  }
  getCurso(nombre: string): Curso {
    nombre = nombre.trim().toLowerCase();
    let esteCurso: Curso = {} as Curso;
    this.api.cursos.forEach((curso: Curso) => {
      curso.claves.forEach((clave: string) => {
        if (nombre.indexOf(clave) > 1) esteCurso = curso;
      });
    });
    return esteCurso;
  }
  creaPassword(email: string): string {
    return email.replace(/(.*)@.*/, '$1');
  }
  generaFecha(date: string = '', s: string = '/'): string {
    const d: string[] = date.split('/') ?? [];
    const f: Date = new Date(d.length > 1 ? Date.parse(d[2] + '-' + d[1] + '-' + d[0]) : Date.now());
    switch (s) {
      case '/':
        return this.api.dosDigitos(f.getDate()) + s + this.api.dosDigitos(f.getMonth() + 1) + s + f.getFullYear();
      case '-':
        return f.getFullYear() + s + this.api.dosDigitos(f.getMonth() + 1) + s + this.api.dosDigitos(f.getDate());
      default:
        return '';
    }
  }
  unificaCursos(data: Participante[]): Participante[] {
    const salida: Participante[] = [];
    data.forEach((participante: Participante) => {
      const posRepetido: number = salida.findIndex((part: Participante) => participante.idnumber === part.idnumber);
      if (posRepetido > -1) {
        if (!salida[posRepetido].course2) {
          salida[posRepetido].course2 = participante.course1;
          salida[posRepetido].enroltimestart2 = participante.enroltimestart1;
          salida[posRepetido].enrolperiod2 = participante.enrolperiod1;
        } else if (!salida[posRepetido].course3) {
          salida[posRepetido].course3 = participante.course1;
          salida[posRepetido].enroltimestart3 = participante.enroltimestart1;
          salida[posRepetido].enrolperiod3 = participante.enrolperiod1;
        }
      } else {
        salida.push(participante);
      }
    });
    return salida;
  }
  siCurso(c: string): boolean {
    const n: string = c.substring(c.length - 1);
    if (n != '2' && n != '3') return true;
    let s: boolean = false;
    this.data.forEach((p: Participante) => (n == '2' && p.course2) || (n == '3' && p.course3) ? s = true : null);
    return s;
  }
  descargarCSV(): void {
    const fecha: string = this.generaFecha('', '-');
    const csv: Participante[] = JSON.parse(JSON.stringify(this.partSel));
    csv.forEach((p: Participante) => {
      p.cohort1 = this.generaFecha(p.cohort1, '-');
      p.enroltimestart1 = this.generaFecha(p.enroltimestart1, '-');
      if (p.enroltimestart2) p.enroltimestart2 = this.generaFecha(p.enroltimestart2, '-');
      if (p.enroltimestart3) p.enroltimestart3 = this.generaFecha(p.enroltimestart3, '-');
    });
    this.excel.exportAsCSV(csv, 'CargaParticipantes_' + fecha);
  }
  editaCampoOrigen(fil: number, col: number): void {
    if (this.campoOrigen.includes(col)) {
      const fila: any = this.data[fil];
      fila[this.campos[2]] = fila[this.campos[18]];
      fila[this.campos[3]] = this.creaPassword(fila[this.campos[4]]);
    }
  }
}
