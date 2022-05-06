// Visto en https://medium.com/@madhavmahesh/exporting-an-excel-file-in-angular-927756ac9857
import { Injectable } from '@angular/core';
import * as FileSaver from 'file-saver';
import * as XLSX from 'xlsx';

export interface ReporteXLSX {
  nombres: string;
  identificacion: string;
  certificado: string;
  curso: string;
  intensidad: string;
  fecha: string;
  ubicacion: string;
  modalidad: string;
  empresa: string;
}

const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
const EXCEL_EXTENSION = '.xlsx';

@Injectable({
  providedIn: 'root'
})
export class ExcelService {
  public exportAsExcelFile(json: any[], excelFileName: string, excluirEncabezado: boolean = false) {
    const worksheet: XLSX.WorkSheet = XLSX.utils.json_to_sheet(json, { skipHeader: excluirEncabezado });
    const workbook: XLSX.WorkBook = { Sheets: { 'data': worksheet }, SheetNames: ['data'] };
    const excelBuffer: any = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    this.saveAsExcelFile(excelBuffer, excelFileName);
  }
  private saveAsExcelFile(buffer: any, fileName: string) {
    const data: Blob = new Blob([buffer], { type: EXCEL_TYPE });
    //FileSaver.saveAs(data, fileName + '_' + new Date().getTime() + EXCEL_EXTENSION);
    FileSaver.saveAs(data, fileName + EXCEL_EXTENSION);
  }
}
