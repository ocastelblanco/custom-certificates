import { Component, OnInit } from '@angular/core';
import { ApiService, Curso, Participante } from 'src/app/servicios/api.service';
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
  modalDescargarCSV: boolean = false;
  constructor(private api: ApiService, private excel: ExcelService) { }
  ngOnInit(): void {
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
  generaFecha(): string {
    const hoy: Date = new Date(Date.now());
    return hoy.getFullYear() + '-' + this.api.dosDigitos(hoy.getMonth() + 1) + '-' + this.api.dosDigitos(hoy.getDate());
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
  siCurso(num: string): boolean {
    return this.data.find((part: Participante) => num == '2' ? part.course2 : part.course3) != undefined;
  }
  descargarCSV(): void {
    this.modalDescargarCSV = true;
  }
}
