<h1><?php echo esc_html__("Logs", "infocob-crm-forms"); ?></h1>

<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="logs-file"><?php echo esc_html__("Files", "infocob-crm-forms"); ?></label>
			</th>
			<td>
				<select id="logs-file" class="all-witdh">
					<option value=""></option>
					<?php foreach(($logs ?? []) as $level => $files): ?>
						<?php foreach(($files ?? []) as $file): ?>
							<option value="<?php echo esc_attr($file["filename_without_ext"] ?? ""); ?>"><?php echo esc_html("[" . $level . "] " . $file["filename_without_ext"] ?? ""); ?></option>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<div id="container-logs">
	<table id="logs" class="display nowrap" style="width:100%">
		<thead>
			<tr>
				<th></th>
				<th><?php echo esc_html__("Subject", "infocob-crm-forms"); ?></th>
				<th><?php echo esc_html__("To", "infocob-crm-forms"); ?></th>
				<th><?php echo esc_html__("Error", "infocob-crm-forms"); ?></th>
				<th><?php echo esc_html__("Date", "infocob-crm-forms"); ?></th>
				<th></th>
				<th><?php echo esc_html__("File", "infocob-crm-forms"); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th></th>
				<th><?php echo esc_html__("Subject", "infocob-crm-forms"); ?></th>
				<th><?php echo esc_html__("To", "infocob-crm-forms"); ?></th>
				<th><?php echo esc_html__("Error", "infocob-crm-forms"); ?></th>
				<th><?php echo esc_html__("Date", "infocob-crm-forms"); ?></th>
				<th></th>
				<th><?php echo esc_html__("File", "infocob-crm-forms"); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
