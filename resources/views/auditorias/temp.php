<select multiple class="form-control" name="audit_' + $(this).val() + '_objective_risks">' + objective_risk_options + '</select>



//-- Info de nueva auditoría --//

//nombre
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_name" class="col-sm-4 control-label">Nombre</label>');
						$('#info_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_' + $(this).val() + '_name" class="form-control"></div>');

						//descripción
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_description" class="col-sm-4 control-label">Descripci&oacute;n</label>');
						$('#info_auditorias').append('<div class="col-sm-8"><textarea rows="3" cols="4" name="audit_' + $(this).val() + '_description" class="form-control"></textarea></div>');

						//recursos
