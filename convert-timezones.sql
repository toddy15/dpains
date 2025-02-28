UPDATE employees SET created_at=CONVERT_TZ(created_at, "Europe/Berlin", "UTC"), updated_at=CONVERT_TZ(updated_at, "Europe/Berlin", "UTC");
UPDATE episodes SET created_at=CONVERT_TZ(created_at, "Europe/Berlin", "UTC"), updated_at=CONVERT_TZ(updated_at, "Europe/Berlin", "UTC");
UPDATE rawplans SET created_at=CONVERT_TZ(created_at, "Europe/Berlin", "UTC"), updated_at=CONVERT_TZ(updated_at, "Europe/Berlin", "UTC");

UPDATE users SET email_verified_at=CONVERT_TZ(email_verified_at, "Europe/Berlin", "UTC"), created_at=CONVERT_TZ(created_at, "Europe/Berlin", "UTC"), updated_at=CONVERT_TZ(updated_at, "Europe/Berlin", "UTC");
UPDATE password_resets SET created_at=CONVERT_TZ(created_at, "Europe/Berlin", "UTC");
UPDATE failed_jobs SET failed_at=CONVERT_TZ(failed_at, "Europe/Berlin", "UTC");
UPDATE personal_access_tokens SET last_used_at=CONVERT_TZ(last_used_at, "Europe/Berlin", "UTC"), created_at=CONVERT_TZ(created_at, "Europe/Berlin", "UTC"), updated_at=CONVERT_TZ(updated_at, "Europe/Berlin", "UTC");
