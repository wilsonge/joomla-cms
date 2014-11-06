SET IDENTITY_INSERT [#__extensions]  ON;

INSERT [#__extensions] ([extension_id], [name], [type], [element], [folder], [client_id], [enabled], [access], [protected], [manifest_cache], [params], [custom_data], [system_data], [checked_out], [checked_out_time], [ordering], [state])
SELECT 452, 'plg_system_session', 'plugin', 'session', 'system', 0, 1, 1, 1, '', '', '', '', 0, '1900-01-01 00:00:00', 0, 0
UNION ALL
SELECT 453, 'plg_user_session', 'plugin', 'session', 'user', 0, 1, 1, 1, '', '{"session_update_flag_name":"refresh"}', '', '', 0, '1900-01-01 00:00:00', 0, 0;

SET IDENTITY_INSERT [#__extensions]  OFF;
